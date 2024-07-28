# Chaine de production

## 1. Les fichiers sources

#### Exemple:

    uint8_t *createBitmapFileHeader(unsigned height, unsigned stride){
    uint32_t fileSize = FILE_HEADER_SIZE + INFO_HEADER_SIZE + stride * height;

    static uint8_t fileHeader[] = {
        0, 0,       // signature
        0, 0, 0, 0, // image file size in bytes
        0, 0, 0, 0, // reserved
        0, 0, 0, 0, // start of pixel array
    };

    fileHeader[0] = 'B';
    fileHeader[1] = 'M';
    fileHeader[2] = (uint8_t)fileSize;
    fileHeader[3] = (uint8_t)(fileSize >> 8);
    fileHeader[4] = (uint8_t)(fileSize >> 16);
    fileHeader[5] = (uint8_t)(fileSize >> 24);
    fileHeader[10] = FILE_HEADER_SIZE + INFO_HEADER_SIZE;

    return fileHeader;
    }
*exemple d'un des fichiers sources*




## 2. La DOC technique

C'est un document qui explique les fonctionnalité des **fichiers sources**.

#### Exemple:
La fonction `void generateBitmapImage(uint8_t const *image, uint32_t height, uint32_t width, char const *imageFileName)` est expliqué dans la doc technique.

## 3. La DOC utilisateur

### En MDQ
En premier lieu la documentation utilisateur doit etre faite en MDQ.


### En html

C'est la doc utilisateur MDQ mais en HTML.
on y retrouve des tabulation:

                <h2>Niveau 2</h2>
                <h3>Niveau 3</h3>
                <h4>Niveau 4</h4>


## 4. Recensement des taches

### Distribution des taches
|livrable|eleve(s)|
|--|--|
|src1.c, src2.c et src3.c|Raphaêl|
|DOC_TECHNIQUE.html|Mattéo|
|DOC_UTILISATEUR.md|Raphaël / Stanislas / Marius |
|DOC_UTILISATEUR.html|Raphaël / Stanislas / Marius |
|TACHES_PERIODE_1.txt|Marius|


### Répartition du travail

- Raphaël  :    53%
- Mattéo   :    18%
- Marius   :    15%
- Stanislas:    14%
