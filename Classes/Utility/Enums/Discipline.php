<?php
namespace JournalTransporterPlugin\Utility\Enums;

/**
 * This is very specific to escholarship
 * Class Disciplines
 * @package JournalTransporterPlugin\Utility\Enums
 */
class Discipline
{
    /**
     * @var string[]
     */
    static $mapping = [
        'disc2932' => 'Architecture',
        'disc1481' => 'Arts and Humanities',
        'disc3688' => 'Business',
        'disc3579' => 'Education',
        'disc3525' => 'Engineering',
        'disc1573' => 'Law',
        'disc1540' => 'Life Sciences',
        'disc3864' => 'Physical Sciences and Mathematics',
        'disc1965' => 'Social and Behavioral Sciences',
    ];

    /**
     * Turn a comment type integer into a label
     *
     * @param $route
     *
     * @return null|string
     */
    static public function getDisciplineName($disciplineKey): ?string
    {
        return @self::$mapping[$disciplineKey] ?: null;
    }
}