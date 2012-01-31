<?php
/**
 * User: matteo
 * Date: 27/01/12
 * Time: 16.16
 *
 * Just for fun...
 */

namespace Cypress\CompassElephantBundle\Collection;

use CompassElephant\CompassProject,
    CompassElephant\CommandCaller,
    CompassElephant\CompassBinary,
    CompassElephant\StalenessChecker\FinderStalenessChecker,
    CompassElephant\StalenessChecker\NativeStalenessChecker;

class CompassProjectCollection implements \ArrayAccess, \Iterator, \Countable
{
    private $compassProjects;
    private $binary;
    private $position;

    /**
     * class constructor
     */
    public function __construct(CompassBinary $binary, $projects)
    {
        $this->binary = $binary;
        $this->position = 0;

        foreach ($projects as $name => $data) {
            if ($data['staleness_checker'] == 'finder') {
                $stalenessChecker = new FinderStalenessChecker($data['path'], $data['config_file']);
            } else if ($data['staleness_checker'] == 'native') {
                $stalenessChecker = new NativeStalenessChecker(new CommandCaller($data['path'], $this->binary));
            }
            $this->compassProjects[] = new CompassProject(
                $data['path'],
                $name,
                $this->binary,
                $stalenessChecker,
                $data['config_file'],
                $data['auto_init']
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->compassProjects[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return scalar scalar on success, integer
     * 0 on failure.
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->compassProjects[$this->position]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     * </p>
     *
     * @return boolean Returns true on success or false on failure.
     * </p>
     * <p>
     *       The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->compassProjects[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->compassProjects[$offset]) ? $this->compassProjects[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     * </p>
     * @param mixed $value  <p>
     *                      The value to set.
     * </p>
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->compassProjects[] = $value;
        } else {
            $this->compassProjects[$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     * </p>
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->compassProjects[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->compassProjects);
    }


}
