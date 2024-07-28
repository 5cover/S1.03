# Titre de niveau 1
## Titre de niveau 2
### Titre de niveau 3
#### Titre de niveau 4

A
B
C

A, B, C doivent être sur la même ligne, séparés par un espace.

1

2

   

    
  
   

3

Il doit y avoir autant d'espace entre 1 et 2 qu'entre 2 et 3.

## Liste

Liste 1:

- élément 1
- élément 2
suite du 2
- élement 3
suite du 3
2eme suite du 3
  
- liste
2

- liste 3

Cette ligne ne fait pas partie de la liste.

Liste4: - pas dans la liste
- une
- liste

- au fait,
- les listes ne supportent pas
- le <b>gras</b>
- ou l'<i>italique</i>.

## Tableaux

### Tableau 1

Ceci est le tableau 1 :

|Titre col1|Titre col2|Titre col3|
|-|-|-|
| 1 | Toto | toto@gmail.com |
| 2 | Lulu | lulu@hotmail.com |

Fin tableau 1


### Tableau 2

Ceci est le tableau 2 :

|id|nom|prenom|
|-|---|--|
|1| <b>Homer</b> (gras) |Simpson|
|2|Moe|<i> Szyslak</i> (ita)|
|3|Charles Montgomery|Burns|

Fin tableau 2

### Tableau 3

Ceci est le tableau 3 (sans en-tête) :

|--|----|-|
|2|d<em>eu</em>x (pas em)|two|
|3|<span style="color:green">trois</span> (pas vert)|three|
|4|q<i>ua</i>tre (ita)|four|(cellule en trop ignorée)|
|5|c<b>in</b>q (gras)|five|
|0|zéro (cellule suivante manquante)|
|8||eight (cellule précédente vide)|
|1|un|one|

Fin tableau 3

### Tableau 4

Ceci est le tableau 4 (sans lignes de détail) : Il ne doit <b>pas</b> être généré.

|abc|bca|
|-|--|

Fin tableau 4

### Tableau 5

Ceci est le tableau 5 (juste un noeud) : Il ne doit <b>pas</b> être généré.

|--|-|

### Tableau 6

Ceci est le tableau 6 (un très gros tableau)

|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|head|
|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|----|
|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|bod1|
|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|bod2|

## Code

Commande linux : `rm     -rf / -no-preserve-root`

Utilisez la commande `./gendoc` pour générer la documentation.

Utilisez la commande `   ./gendoc` pour générer la documentation.

Utilisez la commande `.   /gendoc` pour générer la documentation.

Utilisez la commande `./gendoc   ` pour générer la documentation.

Les `code
inline
multiligne
` sont supportés (les nouvelles lignes ne sont pas conservées).

## Espaces blancs

Les espaces          consécutifs       sont       réduits              |fin.

                     Quoi qu'il en coûte                               |fin!

## Bloc de code

Ceci est un bloc de code :

```
int main() {
    printf("Hello World!\n");
    return EXIT_SUCCESS;
}
```

Bloc de code avec overflow-x :

```
int main() {
    printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");printf("Hello World!\n");
    return EXIT_SUCCESS;
}
```

Bloc de code contenant du Markdown qui ne doit pas être formaté :

```
# Hello

- elt 1
- elt 2

Rendez-vous sur [example.com](https://www.example.com) demain à 16h.

Texte en <b>gras </b>, normal.

Texte en <i> italique</i>, normal.

#### Titre <b>gras</b>, <i>italique</i>, <strong>strong</strong>

Les tags html doivent être préservés
```

Bloc de code contenant des tags HTML qui ne doivent pas être supprimés ni être interprétés par le navigateur :

```
<article>
<h3>Lorem ipsum</h3>
<p>Dolor sit amet</p>
</article>
```

Quatrième bloc de code contenant des nouvelles lignes qui doivent rester en place :

```

Lignes vides au dessus et en dessous

```

fin blocs de code

Ceci n'est pas ```
un bloc de code.
```

Ceci n'est pas non plus ```un bloc de code```.

```Ceci n'est pas non plus un bloc de code```.

Code inline contenant des tags HTML qui ne doivent pas être supprimés : `<strong>Welcome mat</strong>`

Rendez-vous sur [example.com](https://www.example.com) demain à 16h.

Texte en <b>gras </b>, normal.

Texte en <i> italique</i>, normal.

Text en <b><i>gras et italique</i></b>, normal, <i><b>italique et gras</b></i>.

### Titre <b>pas gras</b>, <i>pas italique</i>, <strong>pas strong</strong>

<strong>tout autre tag HTML est ignoré. ceci ne doit pas s'afficher en gras.</strong>

Esperluettes échappées : &amp; != &

Texte quelconque dans un paragraphe.

Texte dans un autre paragraphe.

Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Odio eu feugiat pretium nibh ipsum. Nunc mattis enim ut tellus elementum sagittis. Morbi blandit cursus risus at ultrices. Ac tortor vitae purus faucibus ornare suspendisse sed nisi. Arcu dictum varius duis at consectetur lorem donec massa sapien. Aliquam ut porttitor leo a diam sollicitudin tempor id. Suspendisse interdum consectetur libero id faucibus nisl tincidunt. Tempor orci dapibus ultrices in iaculis. Ut morbi tincidunt augue interdum velit euismod. Leo in vitae turpis massa sed elementum. Egestas erat imperdiet sed euismod nisi. Cras semper auctor neque vitae tempus quam pellentesque nec nam. Viverra justo nec ultrices dui sapien eget mi proin. Enim lobortis scelerisque fermentum dui. Justo eget magna fermentum iaculis eu non diam. At erat pellentesque adipiscing commodo elit at imperdiet dui accumsan. Justo donec enim diam vulputate ut pharetra sit. In est ante in nibh mauris cursus. Risus at ultrices mi tempus imperdiet.

Malesuada proin libero nunc consequat interdum varius sit amet. A scelerisque purus semper eget duis at. Nulla aliquet porttitor lacus luctus accumsan tortor. Mi quis hendrerit dolor magna. Sit amet consectetur adipiscing elit pellentesque. Odio facilisis mauris sit amet massa vitae. Libero volutpat sed cras ornare arcu. Sit amet porttitor eget dolor morbi. Adipiscing elit pellentesque habitant morbi. Risus ultricies tristique nulla aliquet enim tortor at auctor urna.

Nunc lobortis mattis aliquam faucibus purus. Consectetur adipiscing elit ut aliquam purus sit amet luctus. Pulvinar sapien et ligula ullamcorper malesuada proin libero nunc. Sollicitudin aliquam ultrices sagittis orci. Vitae congue eu consequat ac felis donec et odio. Urna id volutpat lacus laoreet. Purus non enim praesent elementum facilisis leo vel fringilla. Donec et odio pellentesque diam volutpat commodo sed egestas. Eget aliquet nibh praesent tristique magna sit amet. Nulla facilisi cras fermentum odio eu. Sagittis nisl rhoncus mattis rhoncus urna neque viverra. Arcu ac tortor dignissim convallis aenean et tortor. Augue mauris augue neque gravida in fermentum et sollicitudin ac. Purus gravida quis blandit turpis cursus. Interdum velit euismod in pellentesque massa. Netus et malesuada fames ac. Lacus luctus accumsan tortor posuere ac. Rhoncus aenean vel elit scelerisque. Odio euismod lacinia at quis risus. Ultrices neque ornare aenean euismod elementum nisi quis eleifend quam.

Amet consectetur adipiscing elit duis tristique sollicitudin nibh sit amet. Ut diam quam nulla porttitor massa id. Ut morbi tincidunt augue interdum velit. Malesuada fames ac turpis egestas. Aenean pharetra magna ac placerat. Tortor condimentum lacinia quis vel eros. Risus quis varius quam quisque id diam. Dignissim sodales ut eu sem integer vitae justo eget. Gravida dictum fusce ut placerat. Mauris augue neque gravida in fermentum et. Praesent tristique magna sit amet. Suspendisse ultrices gravida dictum fusce. Sit amet dictum sit amet justo donec enim diam. Tellus in hac habitasse platea dictumst. Fusce id velit ut tortor pretium viverra suspendisse potenti. Nisi est sit amet facilisis magna etiam tempor orci eu. Mi eget mauris pharetra et ultrices neque ornare. Eget gravida cum sociis natoque penatibus et magnis dis parturient.

Faucibus interdum posuere lorem ipsum dolor sit amet. Ullamcorper a lacus vestibulum sed arcu non odio euismod. Faucibus ornare suspendisse sed nisi. Nam at lectus urna duis convallis convallis tellus id interdum. Mattis pellentesque id nibh tortor. Quis viverra nibh cras pulvinar mattis nunc sed blandit libero. Dui vivamus arcu felis bibendum ut tristique. Odio pellentesque diam volutpat commodo sed egestas. Convallis posuere morbi leo urna. Viverra orci sagittis eu volutpat odio facilisis mauris. Vulputate sapien nec sagittis aliquam. In dictum non consectetur a erat nam at lectus. Sit amet mauris commodo quis imperdiet massa tincidunt nunc pulvinar. Turpis egestas integer eget aliquet nibh praesent tristique magna sit. Elementum integer enim neque volutpat ac tincidunt vitae semper. Id faucibus nisl tincidunt eget nullam non. Pretium aenean pharetra magna ac placerat vestibulum. Odio pellentesque diam volutpat commodo sed egestas egestas fringilla phasellus. Eget nullam non nisi est sit amet facilisis magna. In cursus turpis massa tincidunt dui.