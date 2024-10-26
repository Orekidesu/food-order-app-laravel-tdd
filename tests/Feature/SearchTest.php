<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class SearchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_food_search_accessible()
    {
        $this->get('/')->assertOk();
    }

    public function test_food_search_page_has_all_the_required_page_data()
    {
        // Arrange
        Product::factory()->count(3)->create();
        // Act
        $response = $this->get('/');
        // Assert
        $items = Product::get();
        $response->assertViewIs('search')->assertViewHas('items', $items);
    }
    public function test_food_search_page_shows_the_items()
    {
        // Arrange
        Product::factory()->count(3)->create();

        // Act  
        $response = $this->get('/');

        // Assert
        $items = Product::get();
        $this->get('/')->assertSeeInOrder([$items[0]->name, $items[1]->name, $items[2]->name]);
    }

    public function test_food_can_be_searched_given_query()
    {
        // Arrange
        Product::factory()->create([
            'name' => 'Taco'
        ]);

        Product::factory()->create([
            'name' => 'Pizza'
        ]);

        Product::factory()->create([
            'name' => 'BBQ'
        ]);

        // Assert
        // if the query is taco, only taco should be displayed
        $this->get('/?query=bbq')
            ->assertSee('BBQ')
            ->assertDontSee('Taco')
            ->assertDontSee('Pizza');


        // if there is no query, all items should be displayed 
        $this->get('/')->assertSeeInOrder(['Taco', 'Pizza', 'BBQ']);
    }
}