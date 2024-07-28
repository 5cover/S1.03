/**
 * API d'interface de ligne de commande simple
 *
 * Supporte les commandes sans argument ou avec un seul argument (entier).
 *
 * Source : https://github.com/5cover/MyMalloc
 */

#include <errno.h>
#include <limits.h>
#include <math.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>

/* Interface */

typedef struct
{
    char const *const name;        /** Nom de la commande */
    char const *const description; /** Description de la commande */
    bool const hasArgument;        /** Vrai si la commande attend un argument (entier), faux sinon */
} Command;                         /** Commande pouvant être tapée par l'utilisateur */

typedef struct
{
    Command const *const array; /** Tableau contenant les commandes */
    size_t const count;         /** Nombre de commandes du groupe */
    size_t const maxNameLength; /** Longueur du nom de la commande au plus long nom parmi les commandes du groupe */
} CommandGroup;                 /** Groupe de commandes */

/** \brief Crée un groupe de commandes.
 * \param Command[] commands Tableau de commandes
 * \param size_t count nombre de commandes dans le tableau
 * \return CommandGrouoe Un nouveau groupe de commandes.
 */
CommandGroup createCommandGroup(Command const commands[], size_t count);

/** \brief Affiche un menu en console correspondant à un groupe de commandes.
 * \param CommandGroup commands Le groupe de commandes
 */
void showCommandMenu(CommandGroup commands);

/** \brief Effectue la saisie d'une commande
 * \param CommandGroup commands Groupe de commandes contenant les commandes que l'utilisateur pourra saisir
 * \param int* Sortie : assigné à l'argument passé à la commande saisie, si présent
 * \return Command La commande saisie.
 */

Command const *inputCommand(CommandGroup commands, int *argument);

/* Implementation */

typedef struct {
    char const *name; /** Nom de la commande ou NULL si pas de nom extrait */
    int argument;     /** Valeur de l'argument de la commande ou 0 si pas d'argument extrait */
    bool hasName;     /** Si un nom a pu être extrait */
    bool hasArgument; /** Si un argument a pu être extrait */
} CommandParseResult; /** Résultat de l'analyse d'une commande */

/** \brief Alloue sur le tas assez d'espaces pour stocker une chaîne de caractères
 * \param size_t length La longueur de la chaîne de caractères à stocker
 * \return char* Pointeur vers le bloc de mémoire alloué.
 */
char *allocateString(size_t length);

/** \brief Obtient la commande d'un groupe ayant le nom spécifié.
 * \param CommandGroup commands Le groupe de commandes
 * \param char* commandName Le nom de la commande à rechercher
 * \return Command* Pointeur vers la commande du groupe ayant le nom spécifié, ou NULL si la commande n'a pas été trouvée.
 */
Command const *getCommandFromName(CommandGroup commands, char const *commandName);

/** \brief Obtient la commande d'un groupe correspondant au résultat d'analyse de commande spécifié. Affiche un message d'erreur et retourne NULL si le résultat d'analyse ne correspond pas à une commande du groupe.
 * \param CommandGroup commands Le groupe de commandes
 * \param char* commandName Le résultat d'analyse de commande
 * \return Command* Pointeur vers la commande du groupe correspondant au résultat d'analyse de commande spécifié, ou NULL si aucune commande correspondante n'a été trouvée.
 */
Command const *getCommandFromParseResult(CommandGroup commands, CommandParseResult parsedCommand);

/** \brief Analyse une commande à partir d'une chaîne.
 * \param char* input la chaîne d'entrée
 * \return Un résultat d'analyse de commande.
 */
CommandParseResult parseCommand(char *input);

/** \brief Retourne le nombre de chiffres d'un entier.
 * \param int n Un entier quelconque
 * \return unsigned Le nombre de chiffres de @p n en base 10.
 */
unsigned digitCount(int n);

CommandGroup createCommandGroup(Command const commands[], size_t count)
{
    size_t maxNameLength = 0;

    foreach (Command const, command, commands, count) {
        size_t nameLength = strlen(command->name);
        if (nameLength > maxNameLength) {
            maxNameLength = nameLength;
        }
    }

    return (CommandGroup){
        .array = commands,
        .count = count,
        .maxNameLength = maxNameLength};
}

Command const *inputCommand(CommandGroup commands, int *argument)
{
    size_t const inputLength = commands.maxNameLength + 1 + digitCount(LLONG_MAX);

    char *const input = allocateString(inputLength);

    while (true) {
        printf("\n> ");

        if (gets_s(input, inputLength) == NULL) // get_s failed
        {
            continue;
        }

        CommandParseResult const parsedCommand = parseCommand(input);

        Command const *command = getCommandFromParseResult(commands, parsedCommand);

        if (command == NULL) {
            continue;
        }

        if (parsedCommand.hasArgument) {
            *argument = parsedCommand.argument;
        }

        free(input);
        return command;
    }
}

void showCommandMenu(CommandGroup commands)
{
    foreach (Command const, command, commands.array, commands.count) {
        printf("%*s %s\n", -(int)min(INT_MAX, commands.maxNameLength), command->name, command->description);
    }
}

Command const *getCommandFromParseResult(CommandGroup commands, CommandParseResult parsedCommand)
{
    Command const *command = getCommandFromName(commands, parsedCommand.name);

    if (command == NULL) {
        printf("Unknown command");
        return NULL;
    }

    if (command->hasArgument && !parsedCommand.hasArgument) {
        printf("Argument missing");
        return NULL;
    }

    if (!command->hasArgument && parsedCommand.hasArgument) {
        printf("No argument was expected");
        return NULL;
    }

    return command;
}

Command const *getCommandFromName(CommandGroup commands, char const *commandName)
{
    if (commandName != NULL) {
        foreach (Command const, command, commands.array, commands.count) {
            if (strncmp(command->name, commandName, commands.maxNameLength) == 0) {
                return command;
            }
        }
    }

    return NULL;
}

CommandParseResult parseCommand(char *input)
{
    CommandParseResult result = {
        .name = NULL,
        .argument = 0,
        .hasName = false,
        .hasArgument = false,
    };

    // If available, set value, otherwise return NULL.

    char *context = NULL;
    char const *const separators = " \f\n\r\t\v";

    // Get first token (command name)
    char *token = strtok_s(input, separators, &context);
    if (token == NULL) // input is empty
    {
        return result;
    }

    // Command name is available from here
    result.name = token;
    result.hasName = true;

    // Get next token (integer)
    token = strtok_s(NULL, separators, &context);
    if (token == NULL) {
        return result;
    }

    // Convert argument
    char *endptr;
    errno = 0;
    unsigned int arg = strtoull(token, &endptr, 0);
    if (errno == ERANGE) // entered number out of range
    {
        printf("Argument must be in range [%lld ; %lld]", LLONG_MIN, LLONG_MAX);
    } else if (*endptr != '\0') // conversion failed
    {
        printf("Argument is not a number");
    } else {
        // Store argument
        result.argument = arg;
        result.hasArgument = true;
    }

    return result;
}

char *allocateString(size_t length)
{
    size_t size = length * sizeof(char) + 1;
    void *const ptr = malloc(size);
    if (ptr == NULL) {
        fprintf(stderr, "Allocating %zu bytes failed.", size);
        abort();
    }
    return ptr;
}

unsigned digitCount(int n)
{
    return n == 0 ? 1 : (unsigned)log10((double)llabs(n)) + 1;
}
