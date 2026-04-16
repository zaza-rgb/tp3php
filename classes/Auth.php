<?php
/**
 * Classe d'authentification centralisée
 * Gère les sessions, le hashage des mots de passe, et les rôles utilisateur
 * 
 * Utilisation:
 * - Auth::register($email, $nom, $prenom, $password)
 * - Auth::login($email, $password)
 * - Auth::logout()
 * - Auth::isLoggedIn()
 * - Auth::getCurrentUser()
 * - Auth::isAdmin()
 * - Auth::requireLogin()
 * - Auth::requireAdmin()
 */

class Auth {
    private static $instance = null;
    private $com; // Connexion PDO
    
    private function __construct($pdo_connection) {
        $this->com = $pdo_connection;
        
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Obtenir l'instance singleton
     */
    public static function getInstance($pdo_connection = null) {
        if (self::$instance === null) {
            if ($pdo_connection === null) {
                throw new Exception("Connexion PDO requise pour initialiser Auth");
            }
            self::$instance = new self($pdo_connection);
        }
        return self::$instance;
    }
    
    /**
     * Enregistrer un nouvel utilisateur
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function register($email, $nom, $prenom, $password) {
        // Validation des entrées
        if (empty($email) || empty($nom) || empty($prenom) || empty($password)) {
            return ['success' => false, 'message' => 'Tous les champs sont requis'];
        }
        
        // Validation format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide'];
        }
        
        // Validation longueur mot de passe
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères'];
        }
        
        // Vérifier si l'email existe déjà
        try {
            $stmt = $this->com->prepare("SELECT ref_uti FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
        
        // Hasher le mot de passe
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Insérer le nouvel utilisateur
        try {
            $stmt = $this->com->prepare("
                INSERT INTO utilisateur (nom, prenom, email, password, user_type, is_active, email_verified, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $nom,
                $prenom,
                $email,
                $password_hash,
                'client', // user_type par défaut
                1,        // is_active
                0         // email_verified
            ]);
            
            $ref_uti = $this->com->lastInsertId();
            
            return [
                'success' => true,
                'message' => 'Inscription réussie! Vous pouvez maintenant vous connecter.',
                'user' => [
                    'ref_uti' => $ref_uti,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'user_type' => 'client'
                ]
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()];
        }
    }
    
    /**
     * Connexion d'un utilisateur
     * @return array ['success' => bool, 'message' => string]
     */
    public function login($email, $password) {
        // Validation
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email et mot de passe requis'];
        }
        
        try {
            $stmt = $this->com->prepare("
                SELECT ref_uti, nom, prenom, email, password, user_type, is_active
                FROM utilisateur
                WHERE email = ?
            ");
            
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier si l'utilisateur existe
            if (!$user) {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            }
            
            // Vérifier si le compte est actif
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Ce compte a été désactivé'];
            }
            
            // Vérifier le mot de passe
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            }
            
            // Créer la session
            $_SESSION['user_id'] = $user['ref_uti'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['is_admin'] = ($user['user_type'] === 'admin');
            $_SESSION['login_time'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            
            return [
                'success' => true,
                'message' => 'Connexion réussie!',
                'user_type' => $user['user_type']
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la connexion: ' . $e->getMessage()];
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        // Détruire la session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
        return ['success' => true, 'message' => 'Déconnexion réussie'];
    }
    
    /**
     * Vérifier si un utilisateur est connecté
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Obtenir l'utilisateur actuellement connecté
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'ref_uti' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'user_type' => $_SESSION['user_type'],
            'is_admin' => $_SESSION['is_admin']
        ];
    }
    
    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }
    
    /**
     * Exiger une connexion (redirige vers login si pas connecté)
     */
    public function requireLogin($redirect_to = 'connectedp.php') {
        if (!$this->isLoggedIn()) {
            header("Location: " . $redirect_to . "?redirect=" . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }
    }
    
    /**
     * Exiger d'être admin (redirige vers accueil si pas admin)
     */
    public function requireAdmin($redirect_to = 'accueil.php') {
        if (!$this->isAdmin()) {
            header("Location: " . $redirect_to);
            exit();
        }
    }
    
    /**
     * Changer le mot de passe d'un utilisateur
     */
    public function changePassword($user_id, $old_password, $new_password) {
        // Validation
        if (strlen($new_password) < 6) {
            return ['success' => false, 'message' => 'Le nouveau mot de passe doit contenir au moins 6 caractères'];
        }
        
        try {
            // Récupérer l'utilisateur
            $stmt = $this->com->prepare("SELECT password FROM utilisateur WHERE ref_uti = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Utilisateur non trouvé'];
            }
            
            // Vérifier l'ancien mot de passe
            if (!password_verify($old_password, $user['password'])) {
                return ['success' => false, 'message' => 'Ancien mot de passe incorrect'];
            }
            
            // Hasher et mettre à jour le nouveau mot de passe
            $new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            $stmt = $this->com->prepare("UPDATE utilisateur SET password = ? WHERE ref_uti = ?");
            $stmt->execute([$new_hash, $user_id]);
            
            return ['success' => true, 'message' => 'Mot de passe changé avec succès'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtenir un utilisateur par son ID
     */
    public function getUserById($user_id) {
        try {
            $stmt = $this->com->prepare("
                SELECT ref_uti, nom, prenom, email, user_type, is_active, created_at
                FROM utilisateur
                WHERE ref_uti = ?
            ");
            
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Obtenir un utilisateur par son email
     */
    public function getUserByEmail($email) {
        try {
            $stmt = $this->com->prepare("
                SELECT ref_uti, nom, prenom, email, user_type, is_active, created_at
                FROM utilisateur
                WHERE email = ?
            ");
            
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Lister tous les utilisateurs (admin seulement)
     */
    public function getAllUsers($limit = 50, $offset = 0) {
        try {
            $stmt = $this->com->prepare("
                SELECT ref_uti, nom, prenom, email, user_type, is_active, created_at
                FROM utilisateur
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Désactiver/Activer un utilisateur (admin)
     */
    public function toggleUserStatus($user_id, $is_active) {
        try {
            $stmt = $this->com->prepare("
                UPDATE utilisateur
                SET is_active = ?, user_type = user_type
                WHERE ref_uti = ?
            ");
            
            $stmt->execute([$is_active ? 1 : 0, $user_id]);
            
            return ['success' => true, 'message' => 'Statut de l\'utilisateur mis à jour'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    /**
     * Promouvoir/Rétrograder un utilisateur (admin)
     */
    public function setUserType($user_id, $user_type) {
        if (!in_array($user_type, ['admin', 'client'])) {
            return ['success' => false, 'message' => 'Type d\'utilisateur invalide'];
        }
        
        try {
            $stmt = $this->com->prepare("
                UPDATE utilisateur
                SET user_type = ?
                WHERE ref_uti = ?
            ");
            
            $stmt->execute([$user_type, $user_id]);
            
            return ['success' => true, 'message' => 'Rôle de l\'utilisateur mis à jour'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    /**
     * Supprimer un utilisateur (admin)
     */
    public function deleteUser($user_id) {
        try {
            // Vérifier qu'on ne supprime pas le dernier admin
            if ($this->isLastAdmin($user_id)) {
                return ['success' => false, 'message' => 'Impossible de supprimer le dernier administrateur'];
            }
            
            $stmt = $this->com->prepare("DELETE FROM utilisateur WHERE ref_uti = ?");
            $stmt->execute([$user_id]);
            
            return ['success' => true, 'message' => 'Utilisateur supprimé'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    /**
     * Vérifier s'il y a un dernier admin
     */
    private function isLastAdmin($user_id) {
        try {
            $stmt = $this->com->prepare("
                SELECT ref_uti FROM utilisateur 
                WHERE user_type = 'admin' AND ref_uti != ?
            ");
            
            $stmt->execute([$user_id]);
            return $stmt->rowCount() === 0;
        } catch (PDOException $e) {
            return true;
        }
    }
}

?>
