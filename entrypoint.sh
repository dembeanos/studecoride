#!/usr/bin/env bash
set -e

# Mettre à jour la base ClamAV
echo "🔄 Mise à jour des définitions ClamAV..."
freshclam --quiet

# Démarrer clamd en arrière-plan
echo "▶️ Démarrage de clamd..."
clamd &

# Démarrer Apache en avant-plan
echo "▶️ Démarrage d'Apache..."
apache2-foreground
