<?php
declare(strict_types = 1);

/**
 * @copyright   Copyright (c) 2023  R2H BV (https://r2h.nl). All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Joomla\CMS\Plugin\CMSPlugin;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 *  content - faicons Plugin
 *
 * @package     Joomla.Plugin
 * @subpakage   R2H B.V. faicons
 */
class plgContentFaicons extends CMSPlugin // phpcs:ignore
{
    /**
     * onContentPrepare
     * @param  string $context The content context.
     * @param  mixed  $article The article object.
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
     */
    protected function replaceTags(string $text): string
    {
        $sets = [];

        // Map the old tags to the new ones.
        $types = [
            'fa' => 'fa-solid',
            'fas' => 'fa-solid',
            'far' => 'fa-regular',
            'fal' => 'fa-light',
            'fad' => 'fa-duotone',
            'fat' => 'fa-thin',
        ];

        // Find all the tags.
        if (preg_match_all(
            '/\{(fa(?:(?:l|-light)|(?:r|-regular)|(?:s|-solid)|(?:d|-duotone)|(?:t|-thin))?)\s+?(fa-[a-z0-9-]+)\}/',
            $text,
            $sets,
            PREG_SET_ORDER
        )) {
            foreach ($sets as $matches) {
                $match = $matches[0];
                $type = $matches[1];
                $classes = trim($matches[2]);

                // Replace the old type with the new one.
                $type = $types[$type] ?? $type;

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
