#!/usr/bin/php
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Documentation utilisateur">
    <meta name="author" content="Mattéo Kervadec, Marius Chartier--Le Goff, Raphaël Bardini, Stanislas Rolland">
    <title>Chaine de production</title>
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
    </style>
</head>
<body>
    <header>
        <h1>Documentation utilisateur</h1>
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
            fwrite(STDERR, 'Ligne invalide dans config : "'.rtrim($line, PHP_EOL).'". Abandon de la génération.'.PHP_EOL);
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
    function escape_htmlTags(string $html): string {
        return str_replace(['<', '>'], ['&lt;', '&gt;'], $html);
    }

    function regex_build_stopLookahead(string $token, array $stopSequences): string {
        return'(?:'.$token.'(?!'.implode('|', $stopSequences).'))';
    }

    # Programme principal
    {
        # Initialisation des types de bloc

        # Expression régulières pouvant être utilisées à la fois pour la recherche et la génération d'un type de bloc
        # Le premier groupe correspond au contenu du bloc.
        define('StopEmptyLine', '^ *$');
        define('StopTitle', '^#{1,4} ');
        define('StopCodeBlock', '^```\R');
        define('StopList', '^- \S');

        # Expression régulières utilisées à la fois pour la recherche et la génération de blocs.
        # Le premier groupe représente le contenu du bloc
        define('RegexTitle1', '/# (.+)/');
        define('RegexTitle2', '/## (.+)/');
        define('RegexTitle3', '/### (.+)/');
        define('RegexTitle4', '/#### (.+)/');
        define('RegexCode', '/```\R(.*?)^```$/ms'); # Le marqueur de fin doit être seul sur sa ligne.
        define('RegexParagraph', '/('.regex_build_stopLookahead('.', [StopEmptyLine, StopTitle, StopCodeBlock, StopList]).'+)/ms');

        # Expression régulières de recherche uniquement
        define('RegexSearchList', '/- '.regex_build_stopLookahead('.', [StopEmptyLine, StopTitle, StopCodeBlock]).'+/ms');
        define('RegexSearchTable',
            '/(?:\|(?:.+?\|)+\R)?' # Ligne d'en-tête
            .'\|(?:-+\|)+' # Noeud
            .'(?:\R\|(?:.+?\|)+)+/'); # Lignes de détail
        $blockTypes = [
            # L'odre des valeurs est important.

            # Titre 1
            new BlockType(RegexTitle1,
                fn($block) => preg_replace(RegexTitle1, '<h1>$1</h1>', $block)),
            # Titre 2
            new BlockType(RegexTitle2,
                fn($block) => preg_replace(RegexTitle2, '<h2>$1</h2>', $block)),
            # Titre 3
            new BlockType(RegexTitle3,
                fn($block) => preg_replace(RegexTitle3, '<h3>$1</h3>', $block)),
            # Titre 4
            new BlockType(RegexTitle4,
                fn($block) => preg_replace(RegexTitle4, '<h4>$1</h4>', $block)),
            # Tableau
            new BlockType(RegexSearchTable, 
                'generate_table',
                ['<b>', '<i>']),
            # Liste
            new BlockType(RegexSearchList,
                'generate_list'),
            # Bloc de code
            new BlockType(RegexCode,
                fn($block) => preg_replace_callback(
                    RegexCode,
                    fn($matches) => '<pre><code>'.escape_htmlTags($matches[1]).'</code></pre>',
                    $block),
                [], false),
            # Paragraphe
            new BlockType(RegexParagraph,
                fn($block) => preg_replace(RegexParagraph, '<p>$1</p>', $block),
                ['<b>', '<i>']),
        ];

        # Lire l'entrée standard en totalité
        $markdown = stream_get_contents(STDIN);

        $blocks = BlockType::detect_blocks($blockTypes, $markdown);

        # Afficher le Markdown converti
        echo implode('', array_map(fn($block) => $block->generate(), $blocks));
    }

    function generate_list(string $block): string {
        return '<ul>'.preg_replace('/^- ((?:.(?!^-))*)/ms', '<li>$1</li>', $block).'</ul>';
    }

    function generate_table(string $block): string {
        $lines = explode(PHP_EOL, $block);

        foreach ($lines as $i => $line) {
            if (preg_match('/^\|(?:-+\|)+/m', $line, $nodeMatch)) {
                $nodeLine = $i;
                break;
            }
        }

        assert(!empty($nodeMatch), 'Noeud introuvable');

        $width = substr_count($nodeMatch[0], '|');

        # Générer la ligne d'en-tête
        if ($nodeLine == 1) { # Si un en-tête existe (le noeud n'est pas la première ligne)
            $headerCells = explode('|', substr($lines[$nodeLine - 1], 1), $width);
            if (count($headerCells) == $width) { # si on a des cellules en trop
                array_pop($headerCells); # retirer le dernier élément contenant les cellules en trop
            }
            $lines[$nodeLine - 1] = '<table>';
            $lines[$nodeLine] = '<tr><th>'
                .implode('</th><th>', $headerCells)
                .'</th></tr>';
        } else {
            $lines[$nodeLine] = '<table>';
        }

        # Générer les lignes de détail
        $detailLine = $nodeLine + 1;
        while ($detailLine < count($lines)) {
            $cells = explode('|', substr($lines[$detailLine], 1), $width);
            if (count($cells) == $width) { # si on a des cellules en trop
                array_pop($cells); # retirer le dernier élément contenant les cellules en trop
            }
            $lines[$detailLine] = '<tr><td>'
                .implode('</td><td>', $cells)
                .'</td></tr>';
            ++$detailLine;
        }

        $lines[$detailLine] = '</table>';

        return implode('', $lines);
    }

    final class Block {
        public function __construct(BlockType $type, string $markup) {
            $this->type = $type;
            $this->markup = $markup;
        }

        public BlockType $type;
        public string $markup;

        private const RegexInlineCode = '/`([^`]*)`/';
        private const RegexInlineLink = '/\[(.*?)\]\((.*?)\)/';

        public function generate(): string {
            # Échapper les esperluettes
            # On fait une copie du marquage pour appliquer quelques transformations avant la génération.
            $markup = str_replace('&', '&amp;', $this->markup);

            # Appliquer le formatage en ligne
            if ($this->type->supportsInlineFormatting) {
                $markup = $this->apply_inlineFormatting($markup);
            }

            return $this->type->generate($markup);
        }

        private function apply_inlineFormatting(string $markup): string {
            # Échapper les tags des codes inlines pour pas qu'ils ne soient supprimés avec strip_tags
            $markup = preg_replace_callback(
                static::RegexInlineCode,
                fn($matches) => escape_htmlTags($matches[0]),
                $markup);

            # Retirer les tags inconnus
            $markup = strip_tags($markup, $this->type->allowedTags);

            $markup = preg_replace(
                [static::RegexInlineLink, static::RegexInlineCode],
                ['<a href="$2">$1</a>', '<code class="inline">$1</code>'],
                $markup);

            return $markup;
        }
    }

    final class BlockType {
        # Expression régulière de recherche.
        # Elle trouvera zéro ou un match au début de la chaîne (en prenant en compte l'offset).
        private string $searchRegex;
        private Closure $functionGenerate;

        public function __construct(string $searchRegex, callable $functionGenerate, array $allowedTags = [], bool $supportsInlineFormatting = true) {
            # Préfixer le pattern par \G
            $this->searchRegex = $searchRegex[0].'\G'.substr($searchRegex, 1);
            $this->functionGenerate = Closure::fromCallable($functionGenerate);
            $this->allowedTags = $allowedTags;
            $this->supportsInlineFormatting = $supportsInlineFormatting;
        }

        public array $allowedTags;
        public bool $supportsInlineFormatting;

        public static function detect_blocks(array $blockTypes, string $markdown): array {
            $blocks = [];
            $offset = 0;
            while ($offset < strlen($markdown)) {
                if (ctype_space($markdown[$offset])) {
                    $offset++;
                    continue;
                }
                $block = static::detect_block($blockTypes, $markdown, $offset);
                $blocks[] = $block;
                $offset += strlen($block->markup);
            }
            return $blocks;
        }

        public function generate(string $markup): string {
            return ($this->functionGenerate)($markup);
        }

        private static function detect_block(array $blockTypes, string $input, int $offset): Block {
            foreach ($blockTypes as $blockType) {
                if (preg_match($blockType->searchRegex, $input, $matches, 0, $offset)) {
                    return new Block($blockType, $matches[0]);
                }
            }
            assert(false, "bloc non trouvé");
            exit;
        }
    }
?>
    </main>
</body>