<?php

use Jawira\EntityDraw\Services\ClassFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClassFilter::class)]
class ClassFilterTest extends TestCase
{
  private static array $classNames1 = [
    'App\Entity\User',
    'App\Repository\UserRepository',
    'App\Service\UserService',
    'Doctrine\ORM\EntityManager',
    'Doctrine\DBAL\Connection',
    'Symfony\Component\HttpFoundation\Request',
    'Symfony\Component\HttpFoundation\Response',
    'Symfony\Component\Routing\Router',
    'Laravel\Framework\Auth\AuthenticationManager',
    'Laravel\Framework\Database\QueryBuilder',
    'Acme\Billing\InvoiceGenerator',
    'Acme\Billing\PaymentGateway',
    'Acme\Shop\OrderProcessor',
    'Psr\Log\LoggerInterface',
    'Monolog\Logger',
    'Monolog\Handler\StreamHandler',
  ];

  public static array $classNames2 = [
    'GuzzleHttp\Client',
    'GuzzleHttp\Psr7\Request',
    'Firebase\JWT\JWT',
    'Firebase\JWT\Key',
    'PhpUnit\Framework\TestCase',
    'Stripe\StripeClient',
    'Stripe\Charge',
    'Aws\S3\S3Client',
    'Aws\Credentials\Credentials',
    'Carbon\Carbon',
    'Ramsey\Uuid\Uuid',
    'League\Flysystem\Filesystem',
    'League\Flysystem\Local\LocalFilesystemAdapter',
    'Nette\Application\Application',
    'Nette\Http\RequestFactory',
  ];

  #[DataProvider('classNameProvider')]
  public function testSkipClassName($className, $include, $exclude, $expected): void
  {
    $classFilter = new ClassFilter();
    $result = $classFilter->skipClassName($className, $include, $exclude);
    $this->assertSame($expected, $result);
  }

  public static function classNameProvider(): array
  {
    return [
      // No include nor exclude
      ['', [], [], false],
      ['Hello\\World', [], [], false],
      // Include
      [TestCase::class, [TestCase::class], [], false],
      ['Jawira\Demo\DemoService', self::$classNames1, [], true],
      ['Jawira\Demo\DemoService ', self::$classNames1, [], true],
      ['Jawira\Demo\DemoService', [' Jawira\Demo\DemoService '], [], true],
      ['Acme\Billing\PaymentGateway', self::$classNames1, [], false],
      ['Acme\Billing\PaymentGateway', self::$classNames2, [], true],
      ['Acme\Billing\PaymentGateway', ['Acme\*'], [], false],
      ['Acme\Billing\PaymentGateway', ['App\*', 'Acme\*'], [], false],
      ['Acme\Billing\PaymentGateway', ['App\*'], [], true],
      ['Psr\Log\LoggerInterface', ['App\*', '*Log*'], [], false],
      ['Monolog\Logger', ['App\*', '*\Log*'], [], false],
      ['Monolog\Logger', ['App\*', '*\log*'], [], true],
      // Exclude
      [TestCase::class, [], [TestCase::class], true],
      ['Jawira\Demo\DemoService', [], self::$classNames1, false],
      ['Acme\Billing\PaymentGateway', [], self::$classNames1, true],
      ['Acme\Billing\PaymentGateway', [], self::$classNames2, false],
      ['Acme\Billing\PaymentGateway', [], ['Acme\*'], true],
      ['Acme\Billing\PaymentGateway', [], ['App\*', 'Acme\*'], true],
      ['Acme\Billing\PaymentGateway', [], self::$classNames2, false],
      // Include and exclude
      ['Jawira\Demo\DemoService', [' Jawira\Demo\DemoService '], ['foo'], true],
      ['Monolog\Logger', self::$classNames1, self::$classNames2, false],
      ['Monolog\Logger', self::$classNames1, self::$classNames1, true],
      ['Demo\Class', self::$classNames1, self::$classNames2, true],
    ];
  }
}
