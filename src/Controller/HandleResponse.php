<?php
/**
 * @file
 * Interpret the request and deliver a list of entities that match the query.
 */


/*

where
	- {field: key}
	- {field: {"$op": [mixed]}}

$op
	- $nin: not in
	- $in: in
	- $ne: not equal
	- $e: equal

limit
	- integer

*/

namespace Drupal\morphd\Controller;

use Drupal\Core\Render\RenderContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

use Drupal\morphd\Query\RestEntityQuery;

class HandleResponse implements ContainerAwareInterface {

	use ContainerAwareTrait;

	/**
	 * Deliver the result of the query.
	 */
	public function handle(RouteMatchInterface $route_match, Request $request) {

		$query = new RestEntityQuery($request, $this->container);
		$serializer = $this->container->get('serializer');

		$format = $request->getMimeType() ?: 'json';

		$headers = [
			'Content-Type' => $format,
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Methods' => 'GET',
		];

		$content = $query->execute();

		if ($query->hasError()) {
			return new Response($serializer->serialize($query->getLastError(), $format), 400, $headers);
		}

		return new Response($serializer->serialize($content, 'json'), 200, $headers);
	}

 }
