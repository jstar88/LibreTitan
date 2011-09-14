<?php

if (!defined('INSIDE'))
{
    die(header("location:../../"));
}

function ShowChangelogPage()
{
    global $engine;

    $engine->assignLangFile('CHANGELOG');
    $story = $engine->getKey('changelog');
    foreach ($story as $a => $b)
    {
        $engine->assign('version_number', $a);
        $engine->assign('description', nl2br($b));
        $engine->append('body', $engine->output('changelog_table', true));
    }

    return display($engine->output('changelog_body'));
}

?>