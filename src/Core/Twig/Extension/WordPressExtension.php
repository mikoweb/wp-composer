<?php

/*
 * This file is part of the WordPress Silex package.
 *
 * website: www.mikoweb.pl
 * (c) Rafał Mikołajun <rafal@mikoweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\Twig\Extension;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package WordPress Silex
 * @subpackage Twig_Extension
 */
class WordPressExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_header', 'get_header', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_sidebar', 'get_sidebar', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_footer', 'get_footer', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_search_form', 'get_search_form', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('have_posts', 'have_posts'),
            new \Twig_SimpleFunction('is_day', 'is_day'),
            new \Twig_SimpleFunction('is_month', 'is_month'),
            new \Twig_SimpleFunction('is_year', 'is_year'),
            new \Twig_SimpleFunction('the_post', 'the_post'),
            new \Twig_SimpleFunction('get_template_part', 'get_template_part', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_the_author_meta', 'get_the_author_meta'),
            new \Twig_SimpleFunction('the_author_meta', 'the_author_meta'),
            new \Twig_SimpleFunction('rewind_posts', 'rewind_posts'),
            new \Twig_SimpleFunction('get_post_format', 'get_post_format'),
            new \Twig_SimpleFunction('term_description', 'term_description'),
            new \Twig_SimpleFunction('post_password_required', 'post_password_required'),
            new \Twig_SimpleFunction('number_format_i18n', 'number_format_i18n'),
            new \Twig_SimpleFunction('get_comments_number', 'get_comments_number'),
            new \Twig_SimpleFunction('get_the_title', 'get_the_title'),
            new \Twig_SimpleFunction('get_footer', 'get_footer', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_comment_pages_count', 'get_comment_pages_count'),
            new \Twig_SimpleFunction('get_option', 'get_option'),
            new \Twig_SimpleFunction('previous_comments_link', 'previous_comments_link'),
            new \Twig_SimpleFunction('next_comments_link', 'next_comments_link'),
            new \Twig_SimpleFunction('wp_list_comments', 'wp_list_comments'),
            new \Twig_SimpleFunction('comment_form', 'comment_form'),
            new \Twig_SimpleFunction('the_ID', 'the_ID'),
            new \Twig_SimpleFunction('post_class', 'post_class'),
            new \Twig_SimpleFunction('get_the_category_list', 'get_the_category_list'),
            new \Twig_SimpleFunction('get_permalink', 'get_permalink'),
            new \Twig_SimpleFunction('get_post_type', 'get_post_type'),
            new \Twig_SimpleFunction('edit_post_link', 'edit_post_link'),
            new \Twig_SimpleFunction('wp_link_pages', 'wp_link_pages', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('the_tags', 'the_tags', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('is_single', 'is_single'),
            new \Twig_SimpleFunction('the_content', 'the_content', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('is_home', 'is_home'),
            new \Twig_SimpleFunction('admin_url', 'admin_url'),
            new \Twig_SimpleFunction('do_action', 'do_action'),
            new \Twig_SimpleFunction('language_attributes', 'language_attributes', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('bloginfo', 'bloginfo', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('wp_title', 'wp_title'),
            new \Twig_SimpleFunction('wp_head', 'wp_head', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('body_class', 'body_class', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_header_image', 'get_header_image', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('header_image', 'header_image'),
            new \Twig_SimpleFunction('get_custom_header', 'get_custom_header'),
            new \Twig_SimpleFunction('esc_url', 'esc_url'),
            new \Twig_SimpleFunction('home_url', 'home_url'),
            new \Twig_SimpleFunction('_e', '_e'),
            new \Twig_SimpleFunction('wp_nav_menu', 'wp_nav_menu'),
            new \Twig_SimpleFunction('comments_template', 'comments_template', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('is_front_page', 'is_front_page'),
            new \Twig_SimpleFunction('comments_open', 'comments_open'),
            new \Twig_SimpleFunction('dynamic_sidebar', 'dynamic_sidebar', ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('is_active_sidebar', 'is_active_sidebar'),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wordpress';
    }
}