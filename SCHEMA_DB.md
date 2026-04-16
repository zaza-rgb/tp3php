# 📊 Schéma de la Base de Données E-Commerce

## Tables Créées ✅

### 1. **utilisateur** (Table existante - modifiée)
```
- ref_uti (INT, PK, AUTO_INCREMENT)
- nom (VARCHAR(20))
- prenom (VARCHAR(20))
- email (VARCHAR(50))
- password (VARCHAR(50)) ⚠️ À hasher avec bcrypt
- is_active (BOOLEAN, DEFAULT: TRUE)
- user_type (VARCHAR(50)) - 'admin' ou 'client'
- email_verified (BOOLEAN, DEFAULT: FALSE)
- created_at (TIMESTAMP)
```

### 2. **produit** (Table existante - modifiée)
```
- idprod (INT, PK, AUTO_INCREMENT)
- nomprod (VARCHAR(20))
- prix (INT)
- image (VARCHAR(255))
- typrod (VARCHAR(255)) - Description
- stock (INT, DEFAULT: 0)
- archive (VARCHAR(255))
- couleur (VARCHAR(100), DEFAULT: 'Défaut')
- categorie (VARCHAR(100))
- is_archived (BOOLEAN, DEFAULT: FALSE)
- date_creation (TIMESTAMP)
```

### 3. **commande** (Nouvelles) - Gestion des commandes
```
- idcommande (INT, PK, AUTO_INCREMENT)
- ref_uti (INT, FK → utilisateur.ref_uti)
- montant_total (DECIMAL(10,2))
- statut (ENUM: pending, paid, shipped, delivered, cancelled)
- adresse_livraison (VARCHAR(255))
- telephone (VARCHAR(20))
- date_creation (TIMESTAMP)
- date_modification (TIMESTAMP)
- notes (TEXT)
```

### 4. **commande_produit** - Articleswithin de chaque commande
```
- idcommande_produit (INT, PK, AUTO_INCREMENT)
- idcommande (INT, FK → commande.idcommande)
- idprod (INT, FK → produit.idprod)
- quantite (INT, DEFAULT: 1)
- prix_unitaire (DECIMAL(10,2))
- couleur (VARCHAR(100))
- date_ajout (TIMESTAMP)
```

### 5. **panier** - Panier d'achat temporaire
```
- idpanier (INT, PK, AUTO_INCREMENT)
- ref_uti (INT, FK → utilisateur.ref_uti)
- idprod (INT, FK → produit.idprod)
- quantite (INT, DEFAULT: 1)
- couleur (VARCHAR(100))
- date_ajout (TIMESTAMP)
- UNIQUE: (ref_uti, idprod, couleur)
```

### 6. **couleur_produit** - Variantes de couleurs
```
- idcouleur (INT, PK, AUTO_INCREMENT)
- idprod (INT, FK → produit.idprod)
- couleur (VARCHAR(50))
- stock (INT, DEFAULT: 0)
- date_ajout (TIMESTAMP)
- UNIQUE: (idprod, couleur)
```

### 7. **notification_email** - Logs des emails envoyés
```
- idnotification (INT, PK, AUTO_INCREMENT)
- ref_uti (INT, FK → utilisateur.ref_uti) [NULLABLE]
- email_destinataire (VARCHAR(255))
- type (ENUM: registration, order_confirmation, shipping, password_reset, admin_notification)
- sujet (VARCHAR(255))
- contenu (LONGTEXT)
- statut (ENUM: sent, failed, pending)
- date_envoi (TIMESTAMP)
- date_creation (TIMESTAMP)
```

### 8. **modification_log** - Audit trail (suivi des modifications)
```
- idlog (INT, PK, AUTO_INCREMENT)
- ref_uti (INT, FK → utilisateur.ref_uti) [NULLABLE]
- type (ENUM: produit, commande, utilisateur)
- action (ENUM: create, update, delete, archive)
- id_cible (INT) - ID du produit/commande/utilisateur modifié
- ancien_contenu (TEXT)
- nouveau_contenu (TEXT)
- description (VARCHAR(255))
- date_modification (TIMESTAMP)
```

---

## Diagramme des Relations 🔗

```
utilisateur (1)
    ├─→ (N) commande
    ├─→ (N) panier
    ├─→ (N) notification_email
    └─→ (N) modification_log

produit (1)
    ├─→ (N) commande_produit
    ├─→ (N) panier
    └─→ (N) couleur_produit

commande (1)
    └─→ (N) commande_produit
```

---

## Cas d'Usage des Tables

### 📝 **Processus d'Achat**
1. **Client s'inscrit** → Créer dans `utilisateur` avec `user_type='client'`
2. **Client ajoute produit au panier** → Ajouter dans `panier` (ref_uti, idprod, quantite, couleur)
3. **Client passe commande** → Créer dans `commande` avec statut='pending'
4. **Articles commandés** → Créer dans `commande_produit` (copier du panier)
5. **Vider le panier** → DELETE de `panier` après commande
6. **Email de confirmation** → Ajouter dans `notification_email`

### 👨‍💼 **Gestion Produits (Admin)**
1. **Admin ajoute produit** → Créer dans `produit`
2. **Admin ajoute couleur** → Créer dans `couleur_produit`
3. **Admin modifie produit** → UPDATE + log dans `modification_log`
4. **Admin archive produit** → UPDATE `is_archived=TRUE` + log
5. **Produit rupture de stock** → UPDATE `stock=0` → Afficher "Rupture"

### 📧 **Notifications Email**
- Confirmation d'inscription
- Confirmation de commande
- Notification d'expédition
- Réinitialisation mot de passe
- Messages admin aux clients

---

## Points d'Attention ⚠️

| Problème | Solution |
|----------|----------|
| Mots de passe en clair | Utiliser `password_hash()` avec bcrypt |
| Pas de FOREIGN KEY INDEX | Déjà créé (MUL key visible) |
| Pas de validation | Ajouter validation PHP avant INSERT |
| Pas de pagination | Ajouter LIMIT dans les SELECT |
| Stock négatif possible | Ajouter CHECK constraint |
| Pas de backup automatique | Mettre en place un backup plan |

---

## Prochaines Étapes

### Phase Actuelle (Immédiate)
- ✅ Créer les tables
- ⏳ Implémenter le **panier fonctionnel**
- ⏳ Implémenter le **système de commande**
- ⏳ Implémenter le **système d'authentification robuste**

### Sécurité (Urgente)
- ⚠️ Hasher les mots de passe avec `password_hash()`
- ⚠️ Ajouter les sessions PHP
- ⚠️ Implémenter CSRF protection
- ⚠️ Valider toutes les entrées

### Fonctionnalités (À venir)
- 📧 Système d'envoi d'emails
- 💳 Intégration paiement
- 📊 Dashboard admin
- 📱 Responsive design amélioré
