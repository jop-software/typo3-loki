<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Set\ValueObject\SetList;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\FileProcessor\Composer\Rector\ExtensionComposerRector;
use Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\FileIncludeToImportStatementTypoScriptRector;
use Ssch\TYPO3Rector\Rector\v9\v0\InjectAnnotationRector;
use Ssch\TYPO3Rector\Rector\General\ConvertImplicitVariablesToExplicitGlobalsRector;
use Ssch\TYPO3Rector\Rector\General\ExtEmConfRector;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\ExtbasePersistenceTypoScriptRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();

    // Our goal is to support TYPO3 11.5 and therefor we need to be compatible with PHP 7.4
    $rectorConfig->sets([
        Typo3LevelSetList::UP_TO_TYPO3_11,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::EARLY_RETURN,
    ]);
    $rectorConfig->phpVersion(PhpVersion::PHP_74);

    // In order to have a better analysis from phpstan we teach it here some more things
    $rectorConfig->phpstanConfig(Typo3Option::PHPSTAN_FOR_RECTOR_PATH);

    // FQN classes are not imported by default. If you don't do it manually after every Rector run, enable it by:
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();

    $parameters = $rectorConfig->parameters();

    // this prevents infinite loop issues due to symlinks in e.g. ".Build/" folders within single extensions
    $parameters->set(Option::FOLLOW_SYMLINKS, false);

    $rectorConfig->rule(NameImportingPostRector::class);

    // If you have an editorconfig and changed files should keep their format enable it here
    $parameters->set(Option::ENABLE_EDITORCONFIG, true);

    // The directory we run in, is the Extension itself and not a project.
    $rectorConfig->paths([
        __DIR__ . "/Classes",
//        __DIR__ . "/Configuration",
        __DIR__ . "/Resources",
//        __DIR__ . "/Tests",
    ]);

    // register a single rule
    $rectorConfig->rule(InjectAnnotationRector::class);

    /**
     * Useful rule from RectorPHP itself to transform i.e. GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')
     * to GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class) calls.
     * But be warned, sometimes it produces false positives (edge cases), so watch out
     */
    $rectorConfig->rule(StringClassNameToClassConstantRector::class);


    // Rewrite your extbase persistence class mapping from typoscript into php according to official docs.
    // This processor will create a summarized file with all of the typoscript rewrites combined into a single file.
    // The filename can be passed as argument, "Configuration_Extbase_Persistence_Classes.php" is default.
    $rectorConfig->ruleWithConfiguration(ExtbasePersistenceTypoScriptRector::class, [
        ExtbasePersistenceTypoScriptRector::FILENAME => __DIR__ . '/Configuration/Extbase/Persistence/Classes.php',
    ]);
    // Add some general TYPO3 rules
    $rectorConfig->rule(ConvertImplicitVariablesToExplicitGlobalsRector::class);
    $rectorConfig->ruleWithConfiguration(ExtEmConfRector::class, [
        ExtEmConfRector::ADDITIONAL_VALUES_TO_BE_REMOVED => [],
    ]);
    $rectorConfig->ruleWithConfiguration(ExtensionComposerRector::class, [
        ExtensionComposerRector::TYPO3_VERSION_CONSTRAINT => '',
    ]);

    // Do you want to modernize your TypoScript include statements for files and move from <INCLUDE /> to @import use the FileIncludeToImportStatementVisitor
    $rectorConfig->rule(FileIncludeToImportStatementTypoScriptRector::class);
};
