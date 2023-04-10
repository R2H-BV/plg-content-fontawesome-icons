<?php
/**
 * @copyright	Copyright (c) 2023  R2H BV (https://r2h.nl). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Joomla\CMS\Plugin\CMSPlugin;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 *  content - faicons Plugin
 *
 * @package		Joomla.Plugin
 * @subpakage	R2H B.V. faicons
 */
class plgcontentFaicons extends CMSPlugin {
    /**
     * onContentPrepare
     * @param  string $context The content context.
     * @param  mixed  $article The article object.
     * @return void
     */
    public function onContentPrepare($context, &$article): void // phpcs:ignore
    {
        // Don't run this plugin when the content is being indexed
        if ($context === 'com_finder.indexer') {
            return;
        }

        // Only execute if $article is an object and has a text property
        if (!is_object($article) || !property_exists($article, 'text') || is_null($article->text)) {
            return;
        }

        if (property_exists($article, 'text') && is_string($article->text)) {
            $article->text = $this->replaceTags($article->text);
        }
    }

    /**
     * Replace the FontAwesome tags.
     * @param  string $text The text to replace the tags in.
     * @return string
     */
    protected function replaceTags(string $text): string
    {
        $sets = [];

        if (preg_match_all('/\{(fa(?:l|r|s|b)?)\s+?([a-zA-Z0-9-\s]+)\}/', $text, $sets, PREG_SET_ORDER)) {
            foreach ($sets as $matches) {
                $match = $matches[0];
                $type = $matches[1];
                $classes = trim($matches[2]);

                // Replace old tags too.
                if (strtolower($type) === 'fa') {
                    $type = 'fas';
                }

                $text = str_replace(
                    $match,
                    '<i class="' . $type . ' ' . $classes . '"></i>',
                    $text
                );
            }
        }

        return $text;
    }
}