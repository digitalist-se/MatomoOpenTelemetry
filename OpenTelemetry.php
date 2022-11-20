<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\OpenTelemetry;

use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use Monolog\Logger;
use GuzzleHttp\Psr7\HttpFactory;
use OpenTelemetry\Contrib\Jaeger\Exporter as JaegerExporter;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Span;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\TracerProviderFactory;
use OpenTelemetry\SDK\Common\Time\ClockFactory;
use OpenTelemetry\SDK\Logs\SimplePsrFileLogger;
use OpenTelemetry\SDK\Trace\SpanExporter\LoggerDecorator;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporter;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Log\LoggerHolder;


class OpenTelemetry extends \Piwik\Plugin
{


     /**
     * These are the events that we want to use.
     */
    public function registerEvents()
    {
        return [
            'CronArchive.init.start' => 'CronArchiveStart',
            'CronArchive.end' => 'CronArchiveStop',
            'API.Request.dispatch' => 'APIRequestDispatch'
        ];
    }

    // support archiving just this plugin via core:archive
    public function CronArchiveStart($array)
    {
        LoggerHolder::set(new Logger('otlp', [new StreamHandler('php://stderr')]));
        $transport = (new OtlpHttpTransportFactory())->create('http://otel-collector:4318/v1/traces', 'application/x-protobuf');
        $exporter = new SpanExporter($transport);

        $tracerProvider =  new TracerProvider(
            new SimpleSpanProcessor(
                $exporter
            )
        );
        $tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

        $root = $span = $tracer->spanBuilder('root')->startSpan();
        $scope = $span->activate();
        try {
            $root->addEvent('cron')->setAttribute('status', 'started');
        } finally {
            $root->end();
            $scope->detach();
        }

        $tracerProvider->shutdown();
    }
    // support archiving just this plugin via core:archive
    public function CronArchiveStop($array)
    {
        LoggerHolder::set(new Logger('otlp', [new StreamHandler('php://stderr')]));
        $transport = (new OtlpHttpTransportFactory())->create('http://otel-collector:4318/v1/traces', 'application/x-protobuf');
        $exporter = new SpanExporter($transport);

        $tracerProvider =  new TracerProvider(
            new SimpleSpanProcessor(
                $exporter
            )
        );
        $tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

        $root = $span = $tracer->spanBuilder('root')->startSpan();
        $scope = $span->activate();
        try {
            $root->addEvent('cron')->setAttribute('status', 'stopped');
        } finally {
            $root->end();
            $scope->detach();
        }
        $tracerProvider->shutdown();
    }

    public function APIRequestDispatch(&$finalParameters, $pluginName, $methodName)
    {
        LoggerHolder::set(new Logger('otlp', [new StreamHandler('php://stderr')]));
        $transport = (new OtlpHttpTransportFactory())->create('http://otel-collector:4318/v1/traces', 'application/x-protobuf');
        $exporter = new SpanExporter($transport);

        $tracerProvider =  new TracerProvider(
            new SimpleSpanProcessor(
                $exporter
            )
        );
        $tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

        $root = $span = $tracer->spanBuilder('APIRequestDispatch')->startSpan();
        $scope = $span->activate();
        try {
            $root->addEvent('APIRequestDispatch')->setAttribute('status', 'dispatched');
        } finally {
            $root->end();
            $scope->detach();
        }
        $tracerProvider->shutdown();
    }


}
