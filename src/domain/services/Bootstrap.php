<?php
namespace OrganizeSeries\MultiplesAddon\domain\services;

use DomainException;
use OrganizeSeries\application\Root;
use OrganizeSeries\domain\exceptions\InvalidEntityException;
use OrganizeSeries\domain\exceptions\InvalidInterfaceException;
use OrganizeSeries\domain\interfaces\AbstractBootstrap;
use OrganizeSeries\domain\model\ClassOrInterfaceFullyQualifiedName;
use OrganizeSeries\domain\model\ExtensionIdentifier;
use OrganizeSeries\MultiplesAddon\domain\Meta;
use osMulti;

/**
 * Bootstrap
 * Bootstraps the add-on
 *
 * @package OrganizeSeries\MultiplesAddon\domain\services
 * @author  Darren Ethier
 * @since   1.0.0
 */
class Bootstrap extends AbstractBootstrap
{
    /**
     * Any special initialization logic should go in this method.
     * Examples of things that might happen here are any requirement checks etc.
     *
     * @return bool Return false if you want the bootstrap process to be halted after initializing.
     * @throws DomainException
     * @throws InvalidEntityException
     * @throws InvalidInterfaceException
     */
    protected function initialized()
    {
        $this->loadLegacy();
        //register as an extension
        $this->getExtensionsRegistry()->registerExtension(
            new ExtensionIdentifier(
                'Organize Series Multiples',
                'organize-series-multiples',
                1287,
                self::meta()->getFile(),
                self::meta()->getVersion()
            )
        );
        return true;
    }


    /**
     * Load all legacy files.
     *
     * @throws DomainException
     * @throws InvalidInterfaceException
     */
    private function loadLegacy()
    {
        require_once self::meta()->getBasePath() . 'os-multi-setup.php';
        //let's remove orgSeries core hooks/filter we're replacing
        add_action('init', function(){
            remove_action('quick_edit_custom_box', 'inline_edit_series', 9);
            remove_action('manage_posts_custom_column', 'orgSeries_custom_column_action', 12);
            remove_action('admin_print_scripts-edit.php', 'inline_edit_series_js');
            remove_action('wp_ajax_add_series', 'admin_ajax_series');
            remove_action('admin_print_scripts-post.php', 'orgSeries_post_script');
            remove_action('admin_print_scripts-post-new.php', 'orgSeries_post_script');
            remove_action('delete_series', 'wp_delete_series', 10);
            remove_action('admin_init', 'orgseries_load_custom_column_actions', 10);
        });
        new osMulti;
    }

    /**
     * Any registration of dependencies on the container should happen in this method.
     */
    protected function registerDependencies()
    {
        //noop
    }

    /**
     * Classes should register any routes with the router via this method.
     */
    protected function registerRoutes()
    {
        //noop no routes to register (yet).
    }


    /**
     * Helper for getting the registered Meta class
     *
     * @throws InvalidInterfaceException
     */
    public static function meta()
    {
       return Root::container()->make(
           new ClassOrInterfaceFullyQualifiedName(
               Meta::class
           )
       );
    }

}