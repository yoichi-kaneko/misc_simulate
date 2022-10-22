<?php

namespace App\Calculations\ParticipantsSimulation;

use Faker\Generator;

abstract class CognitiveDegreeAllocator
{
    /*
     * cognitive degreeの配分に合わせて、cognitive degreeのいずれかを抽選で返すクラス
     */
    protected $_faker;
    protected $_cognitive_degree;
    protected $_cognitive_degrees_distribution;

    /**
     * Lottery constructor.
     * @param Generator $faker
     * @param array $cognitive_degrees_distribution
     */
    public function __construct(Generator $faker, array $cognitive_degrees_distribution)
    {
        $this->_faker = $faker;
        $this->_cognitive_degrees_distribution = $cognitive_degrees_distribution;
        $this->initCognitiveDegree($cognitive_degrees_distribution);
    }

    abstract protected function initCognitiveDegree(array $cognitive_degrees_distribution);

    abstract public function allocate(int $potential_participants);
}
