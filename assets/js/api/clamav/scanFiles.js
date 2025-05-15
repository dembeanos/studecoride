const clamav = require('clamav.js');
const fs     = require('fs');

// Récupère le chemin du fichier à scanner en argument
const filePath = process.argv[2];

if (!filePath || !fs.existsSync(filePath)) {
  console.error('Fichier non trouvé :', filePath);
  process.exit(1);
}

// Se connecte au serveur ClamAV sur localhost:3310
const scanner = clamav.createScanner(3310, 'clamav');

// Lance le scan
scanner.scan(filePath, (err, _file, virus) => {
  if (err) {
    console.error('Erreur lors du scan ClamAV:', err);
    process.exit(1);
  }

  if (virus) {
    console.log(`Virus trouvé dans ${filePath} : ${virus}`);
    process.exit(1);
  }

  console.log(`Aucun virus trouvé dans ${filePath}`);
  process.exit(0);
});
