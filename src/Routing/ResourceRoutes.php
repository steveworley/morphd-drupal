<?php

/**
 * @file
 *
 */

namespace Drupal\morphd\Routing;

// Symfony route components.
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Allow access to settings.
use Drupal\Core\Site;

class ResourceRoutes {

	 /**
	  * @var string
	  * The method to call to deliver the routes.
	  */
	 const CONTROLLER = '\Drupal\morphd\Controller\HandleResponse::handle';

	 /**
	  * Access the queryable entities.
	  *
	  * @return array
	  * 	An array of querable entities.
	  */
	 public static function getQueryableEntity() {
		 // return Site\Settings::get('morphd_queryable_entities', []);
		 return [];
	 }

	 /**
	  * {@inheritdoc}
	  */
	 public static function routes() {

		 $route_collection = new RouteCollection();

		 foreach (self::getQueryableEntity() as $type => $info) {
			 $route = new Route(
				// Define the route.
				"/{$type}/query",
				// Route defaults.
				['_controller' => self::CONTROLLER, '_title' => $type],
				// Route requirements.
				['_permission' => 'access content']
			 );

			 // Add the route to the collection.
			 $route_collection->add("morphd.{$type}", $route);
		 }

		 // Test route
		 $route = new Route(
		 	"/morphd_test",
		 	['_controller' => self::CONTROLLER, '_title' => 'Morphd Test'],
		 	['_permission' => 'access content']
		 );

		 $route_collection->add('morphd.test', $route);

		 // Return a list of configured routes.
		 return $route_collection;
	 }
 }
