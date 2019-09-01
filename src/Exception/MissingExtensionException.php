<?php

namespace Beryllium\Cache\Exception;

use Psr\SimpleCache\CacheException;
use RuntimeException;

class MissingExtensionException extends RuntimeException implements CacheException
{
}
