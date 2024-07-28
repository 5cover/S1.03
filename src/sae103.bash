#!/usr/bin/env bash

# Générateur de documentation
# Images docker à pull manuellement : clock, sae103-php, sae103-html2pdf

set -u           # faire une erreur quand une variable inconnue est référencée
set -eo pipefail # s'arrêter quand une erreur a lieu

echoErreur() {
    echo $1 >&2
}
echoUsage() {
    echoErreur "Usage : $0 [--<major|minor|build>]"
}

readonly pre='bigpapoo/' # préfixe des images dans le dépôt public (hors IUT)
readonly regexVersion='VERSION=[[:digit:]]+.[[:digit:]]+.[[:digit:]]+'
readonly regexClient='CLIENT=.*'

echo "SAÉ 1.03 - C'est parti!"

# Vérifier qu'on a le bon nombre d'arguments
if [[ $# -gt 1 ]]; then
    echoErreur "Trop d'arguments ($#)"
    echoUsage
    exit 1
fi

# Lire la version de config
if ! version=$(egrep -x $regexVersion config | cut -d= -f2); then
    echoErreur 'Version manquante ou invalide dans config'
    exit 1
fi

echo "Version : $version"

# Lire le numéro de client
if ! client=$(egrep -x $regexClient config | cut -d= -f2); then
    echoErreur 'Client manquant ou invalide dans config'
    exit 1
fi

echo "Client : $client"

readonly finale_archive_name="$(tr '[:upper:] ' '[:lower:]_' <<<$client)-$version.tar.gz"

if [[ $# -eq 1 ]]; then
    # Incrémenter le numéro de version

    major=$(cut -d. -f1 <<<$version)
    minor=$(cut -d. -f2 <<<$version)
    build=$(cut -d. -f3 <<<$version)

    case $1 in
    '--major')
        ((++major))
        minor=0
        build=0
        ;;
    '--minor')
        ((++minor))
        build=0
        ;;
    '--build')
        ((++build))
        ;;
    *)
        echoErreur "Option inconnue : $1"
        echoUsage
        exit 1
        ;;
    esac

    version="$major.$minor.$build"
    echo "Nouvelle version : $version"

    # mettre le nouveau numéro de version dans config
    sed -ri "s/$regexVersion/VERSION=$version/" config
fi

# Initialisation

echo -n "Création volume : "
docker volume create sae103

echo -n "Conteneur clock démarré avec ID : "
docker container run --name sae103-forever --rm -dv sae103:/work "${pre}clock"

echo "Copie ici->volume..."
for file in config \
               gendoc-user.php doc.md \
               gendoc-tech.php *.c; do
    docker container cp $file sae103-forever:/work
done

# Traitements

echo "Génération C,MD->HTML..."
docker container run --name sae103-gendoc --rm -v sae103:/work -w /work "${pre}sae103-php" sh -c "set -x
php gendoc-tech.php > doc-tech-$version.html
php gendoc-user.php < doc.md > doc-user-$version.html
"
echo "OK!"

echo "Conversion HTML->PDF, PDF->ZIP..."
for command in "html2pdf doc-tech-$version.html doc-tech-$version.pdf" \
                "html2pdf doc-user-$version.html doc-user-$version.pdf" \
                "tar -cf $finale_archive_name -z doc-tech-$version.html doc-tech-$version.pdf doc-user-$version.html doc-user-$version.pdf *.c"; do
    docker container run --name sae103-html2pdf --rm -v sae103:/work -w /work "${pre}sae103-html2pdf" "$command"
done
echo "OK!"

# Récupération du résultat

echo "Copie volume->ici..."
docker container cp sae103-forever:/work/$finale_archive_name $finale_archive_name

# Arrêt

echo -n "Arrêt conteneur clock : "
docker container stop -t 0 sae103-forever

echo -n "Suppression volume : "
docker volume rm sae103

echo 'SAÉ 1.03 - Mission accomplie'
