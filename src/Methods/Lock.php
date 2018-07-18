<?php
/**
 * API locks method
 *
 * Returns a list of locks accociated with the API credentials
 */

namespace drinkynet\Codelocks\Methods;

use drinkynet\Codelocks\Codelocks as Codelocks;

class Lock extends K3connect
{
    // The lock method has been renamed to k3connect in the version 5 API
    // lock functions have moved to the K3connect method class. This class
    // extends that to maintain backward compatability for things that use
    // this wrapper. It may be removed in a future version.
}
