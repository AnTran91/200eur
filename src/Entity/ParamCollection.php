<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParamCollectionRepository")
 * @Gedmo\Loggable
 */
class ParamCollection implements \ArrayAccess, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var array
     *
     * An array containing the entries of this collection.
     *
     * @ORM\Column(type="array", nullable=true)
     * @Gedmo\Versioned
     */
    private $elements;

    /**
     * Initializes a new array.
     */
    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets a native PHP array representation of the collection.
     *
     * @return array
     */
    public function getElements(): ?array
    {
        return $this->elements;
    }

    /**
     * Set a new array.
     *
     * @param array $elements
     *
     * @return ParamCollection
     */
    public function setElements(?array $elements): self
    {
        $this->elements = $elements;

        return $this;
    }

    /**
     * Sets the internal iterator to the first element in the collection and returns this element.
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->elements);
    }

    /**
     * Sets the internal iterator to the last element in the collection and returns this element.
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->elements);
    }

    /**
     * Gets the key/index of the element at the current iterator position.
     *
     * @return int|string
     */
    public function key()
    {
        return key($this->elements);
    }

    /**
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @return mixed
     */
    public function next()
    {
        return next($this->elements);
    }

    /**
     * Gets the element of the collection at the current iterator position.
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|int $key The kex/index of the element to remove.
     *
     * @return mixed The removed element or NULL, if the collection did not contain the element.
     */
    public function remove($key)
    {
        if (! isset($this->elements[$key]) && ! array_key_exists($key, $this->elements)) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element The element to remove.
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement($element)
    {
        $key = array_search($element, $this->elements, true);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
    }

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|int $key The key/index to check for.
     *
     * @return bool TRUE if the collection contains an element with the specified key/index,
     *              FALSE otherwise.
     */
    public function containsKey($key)
    {
        return isset($this->elements[$key]) || array_key_exists($key, $this->elements);
    }

    /**
     * Checks whether an element is contained in the collection.
     * This is an O(n) operation, where n is the size of the collection.
     *
     * @param mixed $element The element to search for.
     *
     * @return bool TRUE if the collection contains the element, FALSE otherwise.
     */
    public function contains($element)
    {
        return in_array($element, $this->elements, true);
    }

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|int $key The key/index of the element to retrieve.
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->elements[$key] ?? null;
    }

    /**
     * Gets all keys/indices of the collection.
     *
     * @return array The keys/indices of the collection, in the order of the corresponding
     *               elements in the collection.
     */
    public function getKeys()
    {
        return array_keys($this->elements);
    }

    /**
     * Gets all values of the collection.
     *
     * @return array The values of all elements in the collection, in the order they
     *               appear in the collection.
     */
    public function getValues()
    {
        return array_values($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|int $key   The key/index of the element to set.
     * @param mixed      $value The element to set.
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->elements[$key] = $value;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->elements[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->elements[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->elements[$offset]) ? $this->elements[$offset] : null;
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return empty($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->elements = [];
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->elements
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->elements
        ) = unserialize($data);
    }
}
