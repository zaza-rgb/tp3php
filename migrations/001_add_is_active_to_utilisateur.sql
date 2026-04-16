-- Migration : Ajouter le champ is_active à la table utilisateur
-- Date : 2026-04-16
-- Description : Permet à l'admin de désactiver les comptes utilisateurs

ALTER TABLE utilisateur ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER password;

-- Vérification
-- SELECT * FROM utilisateur;
