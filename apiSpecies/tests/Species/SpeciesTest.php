<?php


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Document\Species;
use App\Tests\Utils;

class SpeciesTest extends ApiTestCase
{
	/**
	 * @dataProvider dataProvider_invalidMethod
	 */
	public function testInvalidMethod(string $url, string $method): void
	{
		$response = static::createClient()->request($method, $url);
		$this->assertResponseStatusCodeSame(405);
	}

	public function dataProvider_invalidMethod(): array
	{
		return [
			['/api/species'            , 'PUT'],
			['/api/species'            , 'PATCH'],
			['/api/species'            , 'DELETE'],
			['/api/species/1'          , 'PUT'],
			['/api/species/1'          , 'POST'],
			['/api/species/1/validate' , 'PUT'],
			['/api/species/1/validate' , 'GET'],
			['/api/species/1/validate' , 'POST'],
			['/api/species/1/validate' , 'DELETE'],
			['/api/species/1/thumbnail', 'PUT'],
			['/api/species/1/thumbnail', 'GET'],
			['/api/species/1/thumbnail', 'PATCH'],
			['/api/species/1/thumbnail', 'DELETE'],
		];
	}

	public function testAnonUserUnableToFetchUnpublishedSpecies(): void
	{
		$response = static::createClient()->request("GET", "/api/species/1");
		$this->assertResponseStatusCodeSame(401);
	}

	/**
	 * @dataProvider dataprovider_unauthorizedUserUnableToFetchUnpublishedSpecies
	 */
	public function testUnauthorizedUserUnableToFetchUnpublishedSpecies(int $speciesId, array $roles): void
	{
		$token = Utils::generateToken('/api/user/1', '+1 day', 'USER_AUTH', $roles);

		$response = static::createClient()->request("GET", "/api/species/$speciesId", [
			'headers'=>[
				'Authorization'=>'Bearer '.$token
			]
		]);
		$this->assertResponseStatusCodeSame(403);
	}

	public function dataprovider_unauthorizedUserUnableToFetchUnpublishedSpecies(): array
	{
		return [
			[1,  ['ROLE_USER']],
			[1,  ['ROLE_PLANT_EDITOR']],
			[1,  ['ROLE_INVERTEBRATE_EDITOR']],
			[13, ['ROLE_USER']],
			[13, ['ROLE_FISH_EDITOR']],
			[13, ['ROLE_INVERTEBRATE_EDITOR']],
			[16, ['ROLE_USER']],
			[16, ['ROLE_PLANT_EDITOR']],
			[16, ['ROLE_FISH_EDITOR']],
		];
	}

	public function testNotFoundSpecies(): void
	{
		$response = static::createClient()->request("GET", "/api/species/unknown");
		$this->assertResponseStatusCodeSame(404);
	}

	public function testGetCollection(): void
	{
		$response = static::createClient()->request('GET', '/api/species');
		$this->assertResponseStatusCodeSame(200);
		$this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
		$this->assertJsonContains(['hydra:totalItems'=>13]);

		$responseContent = $response->toArray();

		$this->assertCount(12, $responseContent['hydra:member']);
		$this->assertMatchesResourceCollectionJsonSchema(Species::class);
	}

	/**
	 * @dataProvider dataProvider_getFilteredCollection
	 */
	public function testGetFilteredCollection(array $filters, int $numberOfItems, int $totalItems): void
	{
		$response = static::createClient()->request('GET', '/api/species', [
			'query'=>$filters
		]);
		$this->assertResponseStatusCodeSame(200);
		$this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
		$this->assertJsonContains(['hydra:totalItems'=>$totalItems]);

		$responseContent = $response->toArray();

		$this->assertCount($numberOfItems, $responseContent['hydra:member']);
		$this->assertMatchesResourceCollectionJsonSchema(Species::class);
	}

	public function dataProvider_getFilteredCollection(): array
	{
		return [
			[['speciesNaming.scientificName'=>'trichogaster'], 2, 2],
			[['speciesNaming.scientificName'=>'trichogaster chuna'], 1, 1],
			[['unknown'=>'trichogaster'], 12, 13],
			[['speciesNaming.scientificName'=>'unknown'], 0, 0],
		];
	}

	public function testGetFoundItem(): void
	{
		$response = static::createClient()->request('GET', '/api/species/2');
		$this->assertResponseStatusCodeSame(200);
		$this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
		$this->assertJsonContains(['speciesNaming'=>['scientificName'=>'Betta Splendens']]);

		$this->assertMatchesResourceItemJsonSchema(Species::class);
	}

	/**
	 * @dataProvider dataprovider_getFoundUnpublishedItem
	 */
	public function testGetFoundUnpublishedItem(int $itemId, string $token): void
	{
		$response = static::createClient()->request('GET', '/api/species/'.$itemId, [
			'headers'=>[
				'Authorization'=>'Bearer '.$token
			]
		]);
		$this->assertResponseStatusCodeSame(200);
		$this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

		$this->assertMatchesResourceItemJsonSchema(Species::class);
	}

	public function dataprovider_getFoundUnpublishedItem(): array
	{
		$tokenAdmin = Utils::generateToken('/api/user/1', '+1 day', 'USER_AUTH', ['ROLE_ADMIN']);
		$tokenFish = Utils::generateToken('/api/user/1', '+1 day', 'USER_AUTH', ['ROLE_FISH_EDITOR']);
		$tokenPlant = Utils::generateToken('/api/user/1', '+1 day', 'USER_AUTH', ['ROLE_PLANT_EDITOR']);
		$tokenInvertebrate = Utils::generateToken('/api/user/1', '+1 day', 'USER_AUTH', ['ROLE_INVERTEBRATE_EDITOR']);

		return[
			[1 , $tokenAdmin],
			[1 , $tokenFish],
			[13, $tokenAdmin],
			[13, $tokenPlant],
			[16, $tokenAdmin],
			[16, $tokenInvertebrate],
		];
	}


}