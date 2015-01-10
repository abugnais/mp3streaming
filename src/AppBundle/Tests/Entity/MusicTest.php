<?php
namespace AppBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Entity\Music;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MusicTest extends KernelTestCase{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @var string
     */
    private $testDir;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        self::bootKernel();
        $this->em           =   static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->tempDir      =   __DIR__ . '/../../../../web/tmp/';
        $this->testDir      =   __DIR__ . '/../../../../web/test/';

        foreach(glob($this->testDir . '/*') as $file) {
            copy($file, str_replace($this->testDir, $this->tempDir, $file));
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->close();
        foreach(glob($this->tempDir . '*') as $file) {
            unlink($file);
        }
    }

    public function testUploadNoName() {
        $music      = new Music();
        $testFile   = new UploadedFile($this->tempDir . 'heybrother.mp3', 'heybrother.mp3', null, null, null, true);
        $validator  = static::$kernel->getContainer()->get('validator');

        $music->setFile($testFile);
        $this->assertEquals($validator->validate($music)->count(), 0);
        $this->em->persist($music);
        $this->em->flush();
        $this->assertFileExists($music->getAbsolutePath());
        $this->assertEquals($testFile->getClientOriginalName(), $music->getName());
    }

    public function testUploadWithName() {
        $music      =   new Music();
        $testFile   =   new UploadedFile($this->tempDir . 'wakemeup.mp3', 'wakemeup.mp3', null, null, null, true);
        $validator  = static::$kernel->getContainer()->get('validator');
        $testName   =   'NameExample';
        $music->setFile($testFile);
        $music->setName($testName);

        $this->assertEquals($validator->validate($music)->count(), 0);
        $this->em->persist($music);
        $this->em->flush();
        $this->assertEquals($music->name, $testName);
    }

    public function testUploadWrongMime() {
        $music      =   new Music();
        $testFile   =   new UploadedFile($this->tempDir . 'cover.jpg', 'cover.jpg', null, null, null, true);
        $validator  = static::$kernel->getContainer()->get('validator');
        $music->setFile($testFile);
        $this->assertEquals($validator->validate($music)->count(), 1);
    }
}