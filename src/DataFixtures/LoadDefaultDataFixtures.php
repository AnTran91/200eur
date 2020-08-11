<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;

class LoadDefaultDataFixtures extends Fixture
{
	/**
	 * @var string
	 */
	protected $fieldRenovationChoicesDirectory;
	
	/**
	 * @var Finder
	 */
	private static $finder;
	
	/**
	 * LoadFiles constructor.
	 *
	 * @param array $uploadConfigs
	 */
	public function __construct(array $uploadConfigs)
	{
		$this->fieldRenovationChoicesDirectory = $uploadConfigs['fieldRenovationChoicesDirectory'];
	}
	
	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager)
	{
		// init the finder
		self::getBuiltIn();
		
		foreach(self::$finder as $file){
			try{
				$sql = $file->getContents();
				
				$manager->getConnection()->exec($sql);  // Execute native SQL
				$manager->flush();
				
				print "Importing: {$file->getBasename()} ...... Done " . PHP_EOL;
			}catch (\Exception $e){
				print "Importing: {$file->getBasename()} ...... Error Code = {$e->getPrevious()->getCode()}" . PHP_EOL;
			}
		}
		
		$zip = new \ZipArchive();
		if ($zip->open(__DIR__ . '/Resource/RenovationChoices.zip') === TRUE) {
			$zip->extractTo($this->fieldRenovationChoicesDirectory);
			$zip->close();
			print 'Extract: Files ........ Done'. PHP_EOL;
		} else {
			print 'Extract: Files ........ Fail'. PHP_EOL;
		}
	}
	
	/**
	 * Get the SQL.
	 *
	 * @return Finder The built-in mapping.
	 */
	protected static function getBuiltIn(): Finder
	{
		if (self::$finder === null) {
			self::$finder = new Finder();
			self::$finder->in(__DIR__ . '/Resource/SQL');
			self::$finder->name('*.sql');
			self::$finder->files();
			self::$finder->sortByName();
		}
		
		return self::$finder;
	}
}