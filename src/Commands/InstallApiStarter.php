<?php

namespace Dwivedianuj9118\ApiStarter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallApiStarter extends Command
{
    protected $signature = 'api-starter:install';
    protected $description = 'Install Laravel API Starter package';

    protected array $requiredPackages = [
        'laravel/sanctum',
        'tymon/jwt-auth',
        'darkaonline/l5-swagger',
    ];

    public function handle(): int
    {
        $this->info( '🚀 Installing Laravel API Starter...' );
        $this->newLine();

        // 1. Install required packages
        foreach ([
            'laravel/sanctum',
            'tymon/jwt-auth',
            'darkaonline/l5-swagger',
        ] as $package) {
            [ $vendor, $name ] = explode( '/', $package );

            if (!file_exists( base_path( "vendor/{$vendor}/{$name}" ) )) {
                $this->info( "📦 Installing {$package}..." );
                exec( "composer require {$package}" );
            }
        }

        // 2. Publish config
        $this->info( '📦 Publishing API Starter config...' );
        $this->callSilent( 'vendor:publish', [
            '--tag'   => 'api-starter-config',
            '--force' => true,
        ] );

        // 3. Install Sanctum API stack
        if (array_key_exists( 'install:api', Artisan::all() )) {
            $this->info( '🔐 Installing API stack...' );
            $this->call( 'install:api', [ '--no-interaction' => true ] );
            $this->call( 'migrate', [ '--force' => true ] );
        }

        // 4. JWT Secret
        if (array_key_exists( 'jwt:secret', Artisan::all() )) {
            $this->info( '🔑 Generating JWT secret...' );
            $this->callSilent( 'jwt:secret', [ '--force' => true ] );
        }

        // 5. Swagger publish
        if (class_exists( \Darkaonline\L5Swagger\L5SwaggerServiceProvider::class)) {
            $this->info( '📄 Publishing Swagger config...' );
            $this->callSilent( 'vendor:publish', [
                '--tag'   => 'l5-swagger-config',
                '--force' => true,
            ] );
        }

        $this->newLine();
        $this->info( '✅ Laravel API Starter installed successfully!' );
        $this->line( '👉 Next steps:' );
        $this->line( '   - API Health: /api/v1/health' );
        $this->line( '   - Swagger: /api/documentation' );

        return Command::SUCCESS;
    }

    protected function installComposerPackage( string $package ): void
    {
        $this->info( "📦 Installing {$package}..." );

        exec( "composer require {$package}", $output, $status );

        if ($status !== 0) {
            $this->error( "❌ Failed to install {$package}" );
            exit( 1 );
        }
    }

}
