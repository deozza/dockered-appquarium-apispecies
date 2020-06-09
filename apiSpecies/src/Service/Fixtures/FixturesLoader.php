<?php


namespace App\Service\Fixtures;


use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FixturesLoader
{
	private $dm;
	private $encoder;
	private $env;

	public function __construct(UserPasswordEncoderInterface $encoder, DocumentManager $dm)
	{
		$this->dm = $dm;
		$this->encoder = $encoder;
		$this->env = [];
	}

	public function executeFixturesAndReturnEnv(array $fixture): array
	{
		switch($fixture['method'])
		{
			case 'db' :
				$this->executeDbFixtures($fixture['elements'], $fixture['class'], $fixture['save']);
				break;
		}

		$this->dm->flush();

		return $this->env;
	}

	private function executeDbFixtures(array $elements, string $classname, string $propertyToSave): void
	{
		$objects = [];
		foreach($elements as $elementName => $elementContent)
		{
			$objects[$elementName] = $this->buildObjectFromElementContent($classname, $elementContent['body']);
		}

		foreach($objects as $elementName => $object)
		{
			$this->dm->persist($object);
			$this->getPropertyToSaveFromObject($elementName, $propertyToSave, $object);
		}
	}

	private function buildObjectFromElementContent(string $classname, array $content): object
	{
		$object = new $classname();
		foreach($content as $property=>$value)
		{
			if($property === 'password')
			{
				$value = $this->encoder->encodePassword($object, $value);
			}

			if(substr($value, 0, 1) === "@")
			{
				$explodedValue = explode('@',$value);
				$objectFromDb = $this->dm->getRepository($explodedValue[1])->find($explodedValue[2]);

				if(empty($objectFromDb))
				{
					throw new \Exception(sprintf("Object %s was not found ", $value));
				}

				$value = $objectFromDb;
			}

			$function = "set".ucfirst($property);
			$object->$function($value);
		}

		return $object;
	}

	private function getPropertyToSaveFromObject(string $elementName, string $property, object $object)
	{
		try
		{
			$value = $object->$property;
		}
		catch(\Error $e)
		{
			$function = 'get'.ucfirst($property);
			$value = $object->$function();
		}

		$this->env[$elementName."_".$property] = $value;

		if($property === 'id')    $this->env[$elementName."_".$property] = (int) $value;
		if($property === 'token') $this->env[$elementName."_".$property] = 'Bearer '.$value;
	}

	public function storeEnvInFile(array $env)
	{
		ksort($env);

		$postmanFormatedEnv = $this->formatEnvForPostman($env);

		file_put_contents(__DIR__."/../../../tests/Fixtures/env.json", json_encode($postmanFormatedEnv, JSON_PRETTY_PRINT));
	}

	private function formatEnvForPostman(array $env): array
	{
		$postmanFormatedEnv                            = [];
		$postmanFormatedEnv['id']                      = "b1b1e5fa-5f10-4aae-9329-60d240f3db24";
		$postmanFormatedEnv['name']                    = "api";
		$postmanFormatedEnv['_postman_variable_scope'] = "environment";
		$postmanFormatedEnv['_postman_exported_using'] = "Postman/7.16.0";
		$postmanFormatedEnv['_postman_exported_at']    = (new \DateTime('now'))->format('Y-m-d h:i:s');

		$postmanFormatedEnv['values']                  = [];

		foreach($env as $key=>$value)
		{
			$postmanFormatedEnv['values'][] = ['value'=>$value, 'key'=>$key, 'enabled'=>true];
		}

		$postmanFormatedEnv['values'][] = ['value'=>'localhost:8000', 'key'=>'local_url', 'enabled'=>true];

		return $postmanFormatedEnv;

	}
}