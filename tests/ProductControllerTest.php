<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\Product;
use App\Models\Articles;
use App\Models\Cities;
use App\Utility\Flash;
use App\Utility\Upload;

class ProductControllerTest extends TestCase
{
    protected function setUp(): void
    {
        // Set global variables
        $_POST = [];
        $_FILES = [];
        $_SESSION = ['user' => ['id' => 1]];

        // Empty flash messages
        Flash::getMessage();
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testAddProductSuccess()
    {
        // Given
        // Mock data
        $_POST = [
            'name' => 'Produit Test',
            'description' => 'Description du produit',
            'city_id' => 1
        ];
        $_FILES = [
            'picture' => ['tmp_name' => 'test.jpg', 'name' => 'test.jpg', 'size' => 1000]
        ];

        // Mock cities model
        $cityMock = $this->createMock(Cities::class);
        $cityMock->method('getById')->willReturn((object)['id' => 1]);

        // Mock article model
        $articleMock = $this->createMock(Articles::class);
        $articleMock->method('save')->willReturn(1);
        $articleMock->method('attachPicture')->willReturn(true);

        // Mock upload file utility
        $uploadMock = $this->createMock(Upload::class);
        $uploadMock->method('uploadFile')->willReturn('test.jpg');

        $product = new Product([]);
        $product->setCitiesModel($cityMock);
        $product->setUploadUtility($uploadMock);
        $product->setArticlesModel($articleMock);

        // Then
        $this->assertTrue($product->addProduct());
    }

    public function testAddProductNameError()
    {
        // Given
        // Mock data
        $_POST = [
            'name' => '',
            'description' => 'Description du produit',
            'city_id' => 1
        ];
        $_FILES = [
            'picture' => ['tmp_name' => 'test.jpg', 'name' => 'test.jpg', 'size' => 1000]
        ];

        $product = new Product([]);

        // Then
        $this->assertFalse($product->addProduct());
        $this->assertSame(
            json_encode("Une ou plusieurs erreurs sont survenues : \n- Le nom est requis et ne peut pas dépasser 200 caractères \n"),
            json_encode(Flash::getMessage()["body"])
        );
    }

    public function testAddProductDescriptionError()
    {
        // Given
        // Mock data
        $_POST = [
            'name' => 'name',
            'description' => '',
            'city_id' => 1
        ];
        $_FILES = [
            'picture' => ['tmp_name' => 'test.jpg', 'name' => 'test.jpg', 'size' => 1000]
        ];

        $product = new Product([]);

        // Then
        $this->assertFalse($product->addProduct());
        $this->assertSame(
            json_encode("Une ou plusieurs erreurs sont survenues : \n- La description est requise \n"),
            json_encode(Flash::getMessage()["body"])
        );
    }

    public function testAddProductCityError()
    {
        // Given
        // Mock data
        $_POST = [
            'name' => 'name',
            'description' => 'desc'
        ];
        $_FILES = [
            'picture' => ['tmp_name' => 'test.jpg', 'name' => 'test.jpg', 'size' => 1000]
        ];

        $product = new Product([]);

        // Then
        $this->assertFalse($product->addProduct());
        $this->assertSame(
            json_encode("Une ou plusieurs erreurs sont survenues : \n- La ville est requise \n"),
            json_encode(Flash::getMessage()["body"])
        );
    }
}