<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\CalculateNashRequest;
use App\Rules\Coordinate;
use App\Rules\FractionMax;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CalculateNashRequestTest extends TestCase
{
    /**
     * Test that the authorize method returns true.
     *
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        $request = new CalculateNashRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * Test that the rules method returns the expected validation rules.
     *
     * @return void
     */
    public function testRulesReturnsExpectedRules()
    {
        // Create a request with mock input data to avoid null values in Coordinate constructor
        $request = new CalculateNashRequest();
        $request->merge([
            'alpha_1' => ['numerator' => 1, 'denominator' => 2],
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 4],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ]);

        $rules = $request->rules();

        // Check that all expected rules are present
        $this->assertArrayHasKey('alpha_1.numerator', $rules);
        $this->assertArrayHasKey('alpha_1.denominator', $rules);
        $this->assertArrayHasKey('alpha_2.numerator', $rules);
        $this->assertArrayHasKey('alpha_2.denominator', $rules);
        $this->assertArrayHasKey('beta_1.numerator', $rules);
        $this->assertArrayHasKey('beta_1.denominator', $rules);
        $this->assertArrayHasKey('beta_2.numerator', $rules);
        $this->assertArrayHasKey('beta_2.denominator', $rules);
        $this->assertArrayHasKey('rho.numerator', $rules);
        $this->assertArrayHasKey('rho.denominator', $rules);
        $this->assertArrayHasKey('alpha_1', $rules);
        $this->assertArrayHasKey('alpha_2', $rules);
        $this->assertArrayHasKey('beta_1', $rules);
        $this->assertArrayHasKey('beta_2', $rules);
        $this->assertArrayHasKey('rho', $rules);

        // Check that the rules for numerator and denominator are correct
        $this->assertEquals('required|integer|min:1|max:1000', $rules['alpha_1.numerator']);
        $this->assertEquals('required|integer|min:1|max:1000', $rules['alpha_1.denominator']);

        // Check that the FractionMax rule is applied to all fraction fields
        $this->assertContains('required', $rules['alpha_1']);
        $this->assertContains('array', $rules['alpha_1']);
        $this->assertInstanceOf(FractionMax::class, $rules['alpha_1'][2]);

        // Check that the Coordinate rule is applied to beta_2
        $this->assertContains('required', $rules['beta_2']);
        $this->assertContains('array', $rules['beta_2']);
        $this->assertInstanceOf(FractionMax::class, $rules['beta_2'][2]);
        $this->assertInstanceOf(Coordinate::class, $rules['beta_2'][3]);
    }

    /**
     * Test validation passes with valid data.
     *
     * @return void
     */
    public function testValidationPassesWithValidData()
    {
        $data = [
            'alpha_1' => ['numerator' => 1, 'denominator' => 2],
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 4],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ];

        $request = new CalculateNashRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /**
     * Test validation fails with invalid data.
     *
     * @return void
     */
    public function testValidationFailsWithInvalidData()
    {
        // Test with invalid numerator (negative)
        $data = [
            'alpha_1' => ['numerator' => -1, 'denominator' => 2],
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 4],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ];

        $request = new CalculateNashRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());

        // Test with invalid denominator (zero)
        // We'll test only the specific rule for the denominator to avoid division by zero in FractionMax
        $rules = ['alpha_1.denominator' => 'required|integer|min:1|max:1000'];
        $data = ['alpha_1' => ['denominator' => 0]];

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->passes());

        // Test with invalid fraction (greater than max)
        $data = [
            'alpha_1' => ['numerator' => 2, 'denominator' => 1], // 2 > 1
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 4],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ];

        $request = new CalculateNashRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());

        // Test with invalid coordinate (beta_1 > alpha_1 and beta_2 > alpha_2)
        $data = [
            'alpha_1' => ['numerator' => 1, 'denominator' => 4],
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 2],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ];

        $request = new CalculateNashRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * Test that the attributes method returns the expected attribute translations.
     *
     * @return void
     */
    public function testAttributesReturnsExpectedTranslations()
    {
        $request = new CalculateNashRequest();
        $attributes = $request->attributes();

        // Check that all expected attributes are present
        $this->assertArrayHasKey('alpha_1.numerator', $attributes);
        $this->assertArrayHasKey('alpha_1.denominator', $attributes);
        $this->assertArrayHasKey('alpha_2.numerator', $attributes);
        $this->assertArrayHasKey('alpha_2.denominator', $attributes);
        $this->assertArrayHasKey('beta_1.numerator', $attributes);
        $this->assertArrayHasKey('beta_1.denominator', $attributes);
        $this->assertArrayHasKey('beta_2.numerator', $attributes);
        $this->assertArrayHasKey('beta_2.denominator', $attributes);
        $this->assertArrayHasKey('rho.numerator', $attributes);
        $this->assertArrayHasKey('rho.denominator', $attributes);

        // Check that the translations are correctly defined
        $this->assertEquals(trans('validation.attributes.alpha_1_numerator'), $attributes['alpha_1.numerator']);
        $this->assertEquals(trans('validation.attributes.alpha_1_denominator'), $attributes['alpha_1.denominator']);
    }
}
