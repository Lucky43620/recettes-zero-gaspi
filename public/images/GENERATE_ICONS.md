# Génération des icônes PWA

Les icônes PWA doivent être générées aux tailles suivantes :
- 72x72
- 96x96
- 128x128
- 144x144
- 152x152
- 192x192
- 384x384
- 512x512

## Commandes pour générer les icônes

Si vous avez ImageMagick installé :

```bash
# À partir du fichier icon.svg
magick icon.svg -resize 72x72 icon-72x72.png
magick icon.svg -resize 96x96 icon-96x96.png
magick icon.svg -resize 128x128 icon-128x128.png
magick icon.svg -resize 144x144 icon-144x144.png
magick icon.svg -resize 152x152 icon-152x152.png
magick icon.svg -resize 192x192 icon-192x192.png
magick icon.svg -resize 384x384 icon-384x384.png
magick icon.svg -resize 512x512 icon-512x512.png
```

Ou utilisez un service en ligne comme :
- https://realfavicongenerator.net/
- https://www.pwabuilder.com/

## Note

Pour l'instant, le fichier `icon.svg` contient un design de base.
Vous pouvez le remplacer par votre propre logo/design avant de générer les PNG.
