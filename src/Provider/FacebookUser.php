<?php

namespace League\OAuth2\Client\Provider;

class FacebookUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param  array $response
     */
    public function __construct(array $response)
    {
        $this->data = $response;

        if (!empty($response['picture']['data']['url'])) {
            $this->data['picture_url'] = $response['picture']['data']['url'];
        }

        if (isset($response['picture']['data']['is_silhouette'])) {
            $this->data['is_silhouette'] = $response['picture']['data']['is_silhouette'];
        }

        if (!empty($response['cover']['source'])) {
            $this->data['cover_photo_url'] = $response['cover']['source'];
        }
    }

    /**
     * Returns the ID for the user as a string if present.
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getField('id');
    }

    /**
     * Returns the name for the user as a string if present.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getField('name');
    }

    /**
     * Returns the first name for the user as a string if present.
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->getField('first_name');
    }

    /**
     * Returns the last name for the user as a string if present.
     *
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->getField('last_name');
    }

    /**
     * Returns the email for the user as a string if present.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getField('email');
    }

    /**
     * Returns the current location of the user as an array.
     *
     * @return array|null
     */
    public function getHometown(): ?array
    {
        return $this->getField('hometown');
    }

    /**
     * Returns the "about me" bio for the user as a string if present.
     *
     * @return string|null
     * @deprecated The bio field was removed in Graph v2.8
     */
    public function getBio(): ?string
    {
        return $this->getField('bio');
    }

    /**
     * Returns if user has not defined a specific avatar
     *
     * @return boolean
     */

    public function isDefaultPicture(): bool
    {
        return $this->getField('is_silhouette');
    }

    /**
     * Returns the profile picture of the user as a string if present.
     *
     * @return string|null
     */
    public function getPictureUrl(): ?string
    {
        return $this->getField('picture_url');
    }

    /**
     * Returns the cover photo URL of the user as a string if present.
     *
     * @return string|null
     * @deprecated
     */
    public function getCoverPhotoUrl(): ?string
    {
        return $this->getField('cover_photo_url');
    }

    /**
     * Returns the gender for the user as a string if present.
     *
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->getField('gender');
    }

    /**
     * Returns the locale of the user as a string if available.
     *
     * @return string|null
     * @deprecated
     */
    public function getLocale(): ?string
    {
        return $this->getField('locale');
    }

    /**
     * Returns the Facebook URL for the user as a string if available.
     *
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->getField('link');
    }

    /**
     * Returns the current timezone offset from UTC (from -24 to 24)
     *
     * @return float|null
     * @deprecated
     */
    public function getTimezone(): ?float
    {
        return $this->getField('timezone');
    }

    /**
     * Returns the lower bound of the user's age range
     *
     * @return integer|null
     */
    public function getMinAge(): ?int
    {
        return $this->data['age_range']['min'] ?? null;
    }

    /**
     * Returns the upper bound of the user's age range
     *
     * @return integer|null
     */
    public function getMaxAge(): ?int
    {
        return $this->data['age_range']['max'] ?? null;
    }

    /**
     * Returns all the data obtained about the user.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Returns a field from the Graph node data.
     *
     * @return mixed|null
     */
    private function getField(string $key)
    {
        return $this->data[$key] ?? null;
    }
}
