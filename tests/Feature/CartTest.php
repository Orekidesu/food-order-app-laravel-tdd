<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class CartTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_item_can_be_added_to_cart()
    {
        // Arrange
        Product::factory()->count(3)->create();

        // Act
        $this->post('/cart', [
            'id' => 1,
        ])->assertRedirect('/cart')->assertSessionHasNoErrors()->assertSessionHas('cart.0', ['id' => 1, 'qty' => 1]);
    }

    public function test_same_item_cannot_be_added_to_the_cart_twice()
    {
        Product::factory()->create([
            'name' => 'Taco',
            'cost' => 1.5,
        ]);
        Product::factory()->create([
            'name' => 'Pizza',
            'cost' => 2.1,
        ]);
        Product::factory()->create([
            'name' => 'BBQ',
            'cost' => 3.2,
        ]);

        $this->post('/cart', [
            'id' => 1, // Taco
        ]);
        $this->post('/cart', [
            'id' => 1, // Taco
        ]);
        $this->post('/cart', [
            'id' => 2, // Pizza
        ]);

        $this->assertEquals(2, count(session('cart')));
    }

    public function test_cart_page_can_be_accessed()
    {
        Product::factory()->count(3)->create();

        $this->get('/cart')
            ->assertViewIs('cart');
    }

    /*public function test_items_added_to_the_cart_can_be_seen_in_the_cart_page()
    {
        // Make factory for Taco, BBQ, and Pizza

        // Arrange
        Product::factory()->create([
            'name' => 'Taco',
            'cost' => 1.5,
        ]);
        Product::factory()->create([
            'name' => 'Pizza',
            'cost' => 2.1,
        ]);
        Product::factory()->create([
            'name' => 'BBQ',
            'cost' => 3.2,
        ]);

        // Act

        $this->post('/cart', [
            'id' => 1, // Taco
        ]);
        $this->post('/cart', [
            'id' => 3, // BBQ
        ]);


        $cart_items = [
            [
                'id' => 1,
                'qty' => 1,
                'name' => 'Taco',
                'image' => 'some-image.jpg',
                'cost' => 1.5,

            ],
            [
                'id' => 3,
                'qty' => 1,
                'name' => 'BBQ',
                'image' => 'some-image.jpg',
                'cost' => 3.2,
            ]

        ];
        // Assert
        $this->get('/cart')
            ->assertViewIs('cart')
            ->assertViewHas('cart_items', $cart_items)
            ->assertSeeTextInOrder(['Taco', 'BBQ'])
            ->assertDontSee('Pizza');
    }*/
    public function items_added_to_the_cart_can_be_seen_in_the_cart_page()
    {

        Product::factory()->create([
            'name' => 'Taco',
            'cost' => 1.5,
        ]);
        Product::factory()->create([
            'name' => 'Pizza',
            'cost' => 2.1,
        ]);
        Product::factory()->create([
            'name' => 'BBQ',
            'cost' => 3.2,
        ]);

        $this->post('/cart', [
            'id' => 1, // Taco
        ]);
        $this->post('/cart', [
            'id' => 3, // BBQ
        ]);

        $cart_items = [
            [
                'id' => 1,
                'qty' => 1,
                'name' => 'Taco',
                'image' => 'some-image.jpg',
                'cost' => 1.5,
            ],
            [
                'id' => 3,
                'qty' => 1,
                'name' => 'BBQ',
                'image' => 'some-image.jpg',
                'cost' => 3.2,
            ],
        ];

        $this->get('/cart')
            ->assertViewHas('cart_items', $cart_items)
            ->assertSeeTextInOrder([
                'Taco',
                'BBQ',
            ])
            ->assertDontSeeText('Pizza');
    }

    /*public function test_item_can_be_remove_from_the_cart()
    {
        // Arrange

        Product::factory()->create([
            'name' => 'Taco',
            'cost' => 1.5,

        ]);
        Product::factory()->create([
            'name' => 'Pizza',
            'cost' => 2.1,

        ]);
        Product::factory()->create([
            'name' => 'BBQ',
            'cost' => 3.2,

        ]);

        // Act
        // Add items to the session
        session([
            'cart' => [
                ['id' => 2, 'qty' => 1], // Pizza
                ['id' => 3, 'qty' => 3] // BBQ
            ]
        ]);

        // Assert
        // Remove item from the session

        $this->delete('/cart/2') // Pizza
            ->assertRedirect('/cart')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('cart', [
                ['id' => 3, 'qty' => 3] // BBQ
            ]);


        $this->get('/cart')
            ->assertSeeInOrder([
                'BBQ', //name
                '$3.2', //cost
                '3'
            ]); //qty


    }*/
    public function item_can_be_removed_from_the_cart()
    {

        Product::factory()->create([
            'name' => 'Taco',
            'cost' => 1.5,
        ]);
        Product::factory()->create([
            'name' => 'Pizza',
            'cost' => 2.1,
        ]);
        Product::factory()->create([
            'name' => 'BBQ',
            'cost' => 3.2,
        ]);

        // add items to session
        session(['cart' => [
            [
                'id' => 2,
                'qty' => 1
            ], // Pizza
            ['id' => 3, 'qty' => 3], // Taco
        ]]);

        $this->delete('/cart/2') // remove Pizza
            ->assertRedirect('/cart')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('cart', [
                ['id' => 3, 'qty' => 3]
            ]);

        // verify that cart page is showing the expected items
        $this->get('/cart')
            ->assertSeeInOrder([
                'BBQ', // item name
                '$3.2', // cost
                '3', // qty
            ])
            ->assertDontSeeText('Pizza');
    }

    public function test_cart_item_qty_can_be_updated()
    {
        // Arrange

        Product::factory()->create([
            'name' => 'Taco',
            'cost' => 1.5,

        ]);

        Product::factory()->create([
            'name' => 'Pizza',
            'cost' => 2.1,
        ]);

        Product::factory()->create([
            'name' => 'BBQ',
            'cost' => 3.2,
        ]);

        // Act
        // Add items to the session

        session([
            'cart' => [
                ['id' => 1, 'qty' => 1],
                ['id' => 3, 'qty' => 1]
            ]
        ]);

        // Assert
        $this->patch('/cart/3', ['qty' => 5])
            ->assertRedirect('/cart')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('cart', [
                ['id' => 1, 'qty' => 1],
                ['id' => 3, 'qty' => 5]
            ]);

        $this->get('/cart')
            ->assertSeeInOrder(
                [
                    // Item 1
                    'Taco', //name
                    '$1.5', //cost
                    '1', //qty
                    // Item 2
                    'BBQ', //name
                    '$3.2', //cost
                    '5' //qty
                ]
            );
    }
}
