#!/usr/bin/php










<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Documentation technique de MyMalloc">
    <meta name="author" content="Mattéo Kervadec, Marius Chartier--Le Goff, Raphaël Bardini, Stanislas Rolland">
    <style>
        @page {
            margin: 1em 0;
        }

        :root {
            color: #111;
            font-family: 'Open Sans', sans-serif;
            font-size: 18px;
        }
        header {
            break-after: page;
        }
        header, main {
            background: white;
            max-width: 75ch;
        }

        h1, h2, h3, h4 {
            font-family: Helvetica, sans-serif;
            margin-top: .8em;
            margin-bottom: .2em;
            break-after: avoid-page;
        }
        h1 {
            font-size: 2.157em;
            margin-top: 0;
            font-weight: 800;
            text-align: center;
        }
        h2 {
            font-size: 1.882em;
            font-weight: 500;
        }
        h3 {
            font-size: 1.608em;
            font-weight: 500;
        }
        h4 {
            font-size: 1.333em;
            font-weight: 800;
        }

        p {
            line-height: calc(1ex / 0.32);
        }

        ul {
            padding-left: 1em;
        }
        
        pre {
            background-color: #f3f6f8;
            border: .0625em #111 solid;
            padding: 1em;
            width: fit-content;
        }
        code {
            font-family: 'Courier New', monospace;
        }
        code.inline {
            background: #e3e6e8;
            border-radius: .2em;
            padding: .1em .2em;
            white-space: pre-wrap;
        }

        table {
            border-collapse: collapse;
        }
        th {
            border: .125em #111 solid;
            padding: .2em;
        }
        td {
            border: .0625em #111 solid;
            padding: .2em;
        }

        /* Empêcher les éléments pouvant grandir indéfiniment de dépasser le main */
        table, pre {
            break-inside: avoid-page;
            max-width: 100%;
            box-sizing: border-box;
            overflow-x: auto;
            display: block;
        }

        /* Bordures uniquement sur écran */
        @media screen {
            :root {
                background: #111;
            }

            header, main {
                border-radius: 1em;
                padding: 1em;
                margin: 1.618em auto;
            }
        }

        dt {
            font-family: 'Courier New', monospace;
            background: #e3e6e8;
            margin: 1em 0;
        }
    </style>
    <title>Documentation Technique</title>
</head>
<?php
// Mattéo KERVADEC et Marius CHARTIER--LE GOFF 1C2

//------------ Programme Pricipale --------------

function main($files)
{
    /// Parcours les fichiers
    $fileResources = array();
    $arrayDefine = array();
    $arrayFunction = array();
    $arrayStructure = array();
    $arrayGlobales = array();
    foreach ($files as $file) {
        $blocks = explode("\n\n", file_get_contents($file));
        /// Parcour les différents blocs
        foreach ($blocks as $numBlock => $block) {
            // si le bloc contient une constante symbolique
            if (presence($block, "/#define/")) {
                $arrayDefine += readDefine($block);
            }
            // si le bloc contient une déclaration de fonction
            elseif (presence($block, "/\b[a-zA-Z_][a-zA-Z0-9_]*\s*\([^;{}]*\)\s*;/") && presence($block, "/\*\*/")) {
                $arrayFunction += readFunction($block);
            }
            // si le bloc contient une structure
            elseif (presence($block, "/typedef struct/")) {
                $arrayStructure += readStructure($block);
            } elseif (presence($block, "/^const\s+/")) {

                $arrayGlobales += readGlobales($block);
            }
        }
    }
    $fileResources += [
        "define_fichier" => $arrayDefine,
        "fonction_fichier" => $arrayFunction,
        "structure_fichier" => $arrayStructure,
        "globales_fichier" => $arrayGlobales,
    ];
    return $fileResources;
}

//------------ Fonction de Recherche de Chaine de caractère --------------

function presence($line, $string)
{
    /// Recherche dans une ligne une chaine de caractère
    /// renvoie un booléen
    if (preg_match($string, $line) or empty($line))
        $isPresent = true;
    else
        $isPresent = false;
    return $isPresent;
}

//------------ Fonction de Parcours d'extrait de Bloc --------------

function readDefine($block)
{
    // $define = [$entête => "comment"];
    $define = array();
    preg_match_all("/#define\s+(\w+)\s+([^\/\n]+)\s+\/\*\*[\s@]*\\\\?brief\s(.*?)\*\//", $block, $matches, PREG_SET_ORDER);
    foreach ($matches as $line) {
        $define += [rtrim($line[1] . " " . $line[2]) => $line[3]];
    }
    return $define;
}

function readFunction($block)
{
    // $fonction = [$enTete => ["brief" => "...","nomParam1" => "...", "nomParam2" => "...", ..., "return" => ["type" => "brief"]]]];
    $function = array();
    $functionComment = array();

    // les indicateur de brief
    if (presence($block, "/.brief/")) {
        preg_match_all("/.brief\s+(.*)\n/", $block, $brief, PREG_SET_ORDER);
        $functionComment += ["brief" => $brief[0][1]];
    }

    // les indicateurs de param
    preg_match_all("/.param\s+(\b[a-zA-Z0-9_]*\s+[a-zA-Z0-9_]*)\s+(.*)\n/", $block, $param, PREG_SET_ORDER);
    foreach ($param as $comment)
        $functionComment += [rtrim($comment[1]) => $comment[2]];

    // les indicateurs du return
    if (presence($block, "/.return/")) {
        preg_match_all("/(\w*)\s+[a-zA-Z0-9_*]*\s*\([^);]*\);/", $block, $type, PREG_SET_ORDER);
        preg_match_all("/.return\s+(.*)\n/", $block, $return, PREG_SET_ORDER);

        $functionComment += ["return" => [$type[0][1] => $return[0][1]]];
    }

    // L'entête de la fonction
    preg_match_all("/[a-zA-Z0-9_*]*\s+[a-zA-Z0-9_*]*\s*\([^);]*\);/", $block, $enTete, PREG_SET_ORDER);
    $function = [$enTete[0][0] => $functionComment];
    return $function;
}

function readStructure($block)
{
    // $structure = [$nomStruct => ["brief" => briefStruct, $enteteVar1 => "comment Brief", $enteteVar2 => "comment Brief",...]]
    $structure = array();
    $component = array();
    preg_match_all("/}\s*(\w+);\s*\/\*\*\s+(.*)\*\//", $block, $briefStruct, PREG_SET_ORDER);
    $component += ["BriefStruct" => $briefStruct[0][2]];
    preg_match_all("|\s*([a-zA-Z0-9_]*\s[a-zA-Z0-9_]*);\s* /\*\*(.*)\*/\n|U", $block, $matches, PREG_SET_ORDER);
    foreach ($matches as $line) {
        $component += [rtrim($line[1]) => $line[2]];
    }
    $structure = [$briefStruct[0][1] => $component];
    return $structure;
}

function readGlobales($block)
{
    // $globales = ["enTeteGlob1" => briefGlobal, "enTeteGlob1" => briefGlobal, "enTeteGlob1" => briefGlobal]
    $globales = array();
    preg_match_all('/\bconst\s+(\w+)\s+(\w+)\s*;/', $block, $entete_matches, PREG_SET_ORDER);
    preg_match_all('/\/\*\*(.*?)\*\//', $block, $commentaire_matches, PREG_SET_ORDER);
    for ($index = 0; $index < count($entete_matches); $index++) {
        $globales += [rtrim($entete_matches[$index][0]) => $commentaire_matches[$index][1]];
    }
    return $globales;
}

//entre dans un tableau le nom de tout les fichiers qui finnisent pas .c
$files = glob("*.c");

//recherche dans tout les fichiers c la documentation
$arrayDocumentation = main($files);

// print_r($arrayDocumentation);
?>

<body>
    <header>
        <h1>Documentation technique</h1>
        <?php
        # Constantes symboliques
        define('ConfigFilename', 'config');
        define('ExitCodeInvalidConfig', 1);

        # Lecture du fichier de configuration
        $config = [];
        foreach (file(ConfigFilename) as $line) {
            $entry = explode('=', $line);
            if (count($entry) != 2) {
                # Rtrim pour retirer le \n final de la ligne
                fwrite(STDERR, 'Ligne invalide dans config : "' . rtrim($line, PHP_EOL) . '". Abandon de la génération.' . PHP_EOL);
                exit(ExitCodeInvalidConfig);
            }
            $config[$entry[0]] = rtrim($entry[1], PHP_EOL);
        }
        ?>
        <p><strong>Client</strong>&nbsp;: <?php echo $config['CLIENT']; ?></p>
        <p><strong>Produit</strong>&nbsp;: <?php echo $config['PRODUIT']; ?></p>
        <p><strong>Version</strong>&nbsp;: <?php echo $config['VERSION']; ?></p>
        <p><strong>Date de génération</strong>&nbsp;: <time datetime="<?php echo date('Y-m-d') ?>"><?php echo date('j/m/Y'); ?></time></p>
    </header>

    <main>

        <?php
        function coupe_chaine($chaine)
        {
            $mots = explode(" ", trim($chaine));
            return $mots;
        }

        foreach ($arrayDocumentation as $categorie => $objet) {
            if ($categorie == "define_fichier") { ?>
                <section>
                    <h2>DEFINES</h2>
                    <dl>
                        <?php

                        foreach ($objet as $clef => $description) {
                            $nom_define = $clef;
                            $brief_define = $description;
                        ?> <dt><?php echo $nom_define; ?></dt>
                            <dd><?php echo $brief_define; ?> </dd>


                        <?php
                        }
                        ?>
                    </dl>
                </section><?php
                        } elseif ($categorie == "fonction_fichier") {
                            ?><section>
                    <h2>FONCTIONS</h2>
                    <dl><?php
                            foreach ($objet as $clef => $description) {
                                $entête_fonction = $clef;
                                $nb_param = 0;
                                $avec_return = false;
                        ?>

                            <dt><code> <?php echo $entête_fonction; ?> </code></dt>
                            <dd>

                                <?php
                                foreach ($description as $key => $value) {
                                    if ($key == "brief") {
                                        $brief_fonction = $value;
                                ?><p><?php echo $brief_fonction ?></p>

                                        <?php
                                    } elseif ($key == "return") {
                                        $avec_return = true;
                                        if ($nb_param > 0) { ?></tbody>
                                            </table><?php
                                                }
                                                foreach ($value as $type_return => $brief_return) {
                                                    ?>
                                            <p><strong>Retour</strong>&nbsp;: <?php echo $brief_return; ?></p>
                                        <?php
                                                }
                                            } else {
                                                $mots = coupe_chaine($key);
                                                $nom_param = $mots[1];
                                                $type_param = $mots[0];
                                                $brief_param = $value;
                                                $nb_param += 1;
                                                if ($nb_param == 1) {
                                        ?><table>
                                                <caption>Parametres</caption>
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Nom</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                }

                                                ?>
                                                <tr>
                                                    <td><?php echo $type_param; ?></td>
                                                    <td><?php echo $nom_param; ?></td>
                                                    <td><?php echo $brief_param; ?></td>
                                                </tr>
                                                <?php
                                            }
                                            
                                        } if ($avec_return == false) {
                                                if ($nb_param > 0) { ?>
                                                </tbody>
                                            </table><?php
                                                }
                                            }?>
                            </dd><?php
                                } ?><?php
                                    ?>
                    </dl>
                </section><?php
                        } elseif ($categorie == "structure_fichier") {
                            ?>
                <section>
                    <h2>STRUCTURES</h2>
                    <dl><?php
                            foreach ($objet as $clef => $description) {
                                $nom_struct = $clef; ?>
                            <dt><code><?php echo $nom_struct; ?></code></dt>
                            <dd>

                                    
                                        <?php
                                        foreach ($description as $key => $value) {
                                            if ($key == "BriefStruct") {
                                                $brief_struct = $value;
                                        ?><p><?php echo $brief_struct; ?></p><?php
                                                                                ?><table>
                                                    <caption>Composants</caption>
                                                    <thead>
                                                        <tr>
                                                            <th>Nom</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php } else {

                                                    $nom_element = $key;
                                                    $brief_element = $value;
                                                    ?><tr>
                                                            <td><em><?php echo $nom_element    ?></em></td>
                                                            <td><?php echo $brief_element     ?> </td>
                                                    <?php
                                                }
                                            } ?></tbody>
                                                </table>
                            </dd><?php
                                } ?>
                    </dl>
                </section><?php
                        } elseif ($categorie == "globales_fichier") { ?>
                <section>
                    <h2>GLOBALES</h2>
                    <dl>

                        <?php
                            foreach ($objet as $clef => $description) {
                                $entête_globales = $clef;
                                $brief_globales = $description;
                        ?> <dt><code><?php echo $entête_globales     ?></code></dt>
                            <dd><?php echo $brief_globales     ?></dd>
                        <?php



                            }
                        ?>
                    </dl>
                </section><?php

                        }
                    }

                            ?>
    </main>
</body>

</html>


