<?php

namespace NSWDPC\Messaging\Taggable;

/**
 * Trait a {@link \SilverStripe\Control\Email\Email} subclass can use
 * to provide custom parameter handling for a {@link \SilverStripe\Control\Email\Mailer}
 *
 * @author James
 *
 */
trait CustomParameters
{
    /**
     * Custom parameters stored, an array of parameters
     */
    private $customParameters = [];

    /**
     * Set custom parameters
     */
    public function setCustomParameters(array $args): static
    {
        $this->customParameters = $args;
        return $this;
    }

    /**
     * Return custom parameters
     */
    public function getCustomParameters(): array
    {
        return $this->customParameters;
    }

    /**
     * Clear all custom parameters
     */
    public function clearCustomParameters(): static
    {
        $this->customParameters = [];
        return $this;
    }
}
