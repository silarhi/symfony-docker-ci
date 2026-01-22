# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Symfony 7.4 demonstration application showcasing continuous integration/deployment with Docker and CircleCI. The project demonstrates:

- Custom two-factor authentication implementation
- Dynamic image processing with League Glide
- Hybrid frontend: Twig templates + React components via Webpack Encore
- Containerized deployment workflow
- Enterprise-grade PHP tooling (PHPStan level 9, PHP-CS-Fixer, Rector)

## Development Commands

### PHP/Symfony

```bash
# Install PHP dependencies
composer install

# Run Symfony console commands
bin/console <command>

# Clear cache
bin/console cache:clear

# Lint Symfony configuration
bin/console lint:container
bin/console lint:yaml config --parse-tags
bin/console lint:twig templates
```

### Frontend Assets

```bash
# Install JavaScript dependencies
yarn install

# Build assets for development
yarn dev

# Build assets for production
yarn build

# Watch for changes
yarn watch

# Run development server with hot reload
yarn dev-server
```

### Code Quality

```bash
# PHP linting and static analysis
vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php
vendor/bin/phpstan analyse
vendor/bin/rector process --dry-run

# JavaScript/CSS linting
yarn lint                  # Run all linters
yarn lint:eslint          # ESLint only
yarn lint:prettier        # Prettier only

# Format code
yarn format               # Format all
yarn format:eslint        # Fix ESLint issues
yarn format:prettier      # Format with Prettier
```

### Docker

```bash
# Build Docker image
docker build -t silarhi/symfony-docker-ci .

# Build with version metadata
docker build \
  --build-arg APP_VERSION=1.0.0 \
  --build-arg GIT_COMMIT=$(git rev-parse --short HEAD) \
  -t silarhi/symfony-docker-ci .
```

### Git Hooks

The project uses Husky for pre-commit hooks that run:

- ESLint with auto-fix on JS/JSX files
- Prettier on SCSS/MD files
- PHP-CS-Fixer on PHP files
- Twig-CS-Fixer on Twig templates

## Architecture

### Two-Factor Authentication Flow

The application implements a custom 2FA system using multiple authenticators:

1. **LoginFormAuthenticator** (src/Security/LoginFormAuthenticator.php)
    - Handles initial username/password authentication
    - First authenticator in the chain

2. **TwoFactorsAuthenticator** (src/Security/TwoFactorsAuthenticator.php)
    - Validates TOTP codes using Google2FA
    - Second authenticator in the chain
    - Works in conjunction with DoubleAuthentificationSubscriber

3. **DoubleAuthentificationSubscriber** (src/EventSubscriber/DoubleAuthentificationSubscriber.php)
    - Manages 2FA workflow via event subscription
    - Grants `ROLE_2FA_SUCCEED` upon successful 2FA completion
    - Redirects to `/setup-2FA` if user hasn't set up 2FA yet
    - Uses session storage for QR code secrets during setup

**Key Implementation Detail**: The security system uses a custom role `ROLE_2FA_SUCCEED` to track 2FA completion status. This role is granted dynamically by the event subscriber and is used to protect sensitive routes.

### Image Processing

The application uses League Glide for on-the-fly image manipulation:

- **AssetsController** (src/Controller/AssetsController.php) - Serves images via `/assets/{path}` route
- **AssetExtension** (src/Twig/AssetExtension.php) - Provides `asset_url()` Twig function with HMAC signature for security
- Images are processed dynamically based on URL parameters (resize, crop, filters, etc.)
- Security: Routes are protected with HMAC signatures to prevent manipulation

### Frontend Architecture

**Hybrid Approach**:

- Server-rendered Twig templates for main application structure
- React components for interactive features (e.g., index page with API data)
- Stimulus controllers for progressive enhancement
- Hotwire Turbo for SPA-like navigation

**Entry Points** (webpack.config.js):

- `app` - Main application CSS/JS (base.html.twig)
- `index` - React-based index page (assets/js/pages/index.jsx)

**Build Process**:

- Webpack Encore compiles assets to `public/build/`
- Development: Non-hashed filenames
- Production: Content-hashed filenames for cache busting
- SCSS compiled with dart-sass
- React JSX transpiled with Babel

### Dependency Injection & Services

The application follows Symfony's autowiring conventions:

- Services in `src/` are auto-registered and autowired
- Configuration in `config/services.yaml`
- No manual service definitions needed for standard services

## Code Quality Standards

### PHP Standards

- **PSR-12** coding style via PHP-CS-Fixer with Symfony ruleset
- **PHPStan level 9** - Maximum static analysis strictness
- **Header comments** - All PHP files must include SILARHI copyright header
- **Strict types** - Use `declare(strict_types=1);` in all PHP files
- **Import optimization** - Global namespace imports for classes

### PHP-CS-Fixer Configuration

Notable rules from `.php-cs-fixer.dist.php`:

- Symfony ruleset with risky rules enabled
- Concatenation with one space: `'hello' . ' world'`
- Native function invocation optimization
- Global namespace imports for classes
- Alpha-sorted imports

### JavaScript/TypeScript Standards

- ESLint with React plugin
- Prettier for formatting
- React 19 with hooks patterns
- ES6+ syntax required

## CI/CD Pipeline

### CircleCI Workflow

Located in `.circleci/config.yml`:

**Build Job**:

1. Checkout code
2. Build Docker image with version metadata
3. Tag as `latest` for main branch
4. Push to Docker Hub (silarhi/symfony-docker-ci)

**Deploy Job** (main branch only):

- SSH to production server
- Execute `deploy.sh` script

### GitHub Actions

Located in `.github/workflows/continuous-integration.yml`:

**Lint JS/JSX Job**:

- ESLint and Prettier checks
- Runs on all PRs and pushes

**QA Checks**:

- Uses Laminas CI matrix for multi-environment testing
- Symfony console linting (container, YAML, Twig)
- PHP static analysis

## Configuration Notes

### Security Configuration

- **Password hashing**: Plaintext (demo only - not for production!)
- **User provider**: In-memory with single test user (test/test)
- **Firewall**: Custom authenticator chain for 2FA
- **Access control**: `/setup-2FA` requires `ROLE_USER`
- **CSRF protection**: Enabled with header check for Turbo

### Environment Variables

Required for deployment (see config/packages/twig.yaml and Dockerfile):

- `APP_VERSION` - Application version (injected during build)
- `GIT_COMMIT` - Git commit hash (injected during build)
- `APP_ENV` - Environment (prod/dev/test)

### Docker Multi-Stage Build

The Dockerfile uses a three-stage build process:

1. **php_builder**: Install PHP dependencies with Composer
2. **node_builder**: Build frontend assets with Yarn + Webpack Encore
3. **Final stage**: Combine artifacts, optimize autoloader, warm cache

**Important**: The build process:

- Uses `--classmap-authoritative` for production optimization
- Warms Symfony cache before deployment
- Creates `.env.local.php` for environment variable caching
- Excludes `assets/` and `docker/` directories from final image

## File Structure

```
src/
├── Controller/        # HTTP request handlers
├── EventSubscriber/   # Symfony event listeners (2FA logic)
├── Form/             # Symfony form types (2FA activation)
├── Security/         # Authenticators (login + 2FA)
└── Twig/             # Custom Twig extensions (asset URLs)

templates/            # Twig templates
├── base.html.twig   # Main layout with navigation
├── security/        # Login, 2FA setup pages
└── default/         # Homepage templates

assets/
├── js/
│   ├── app.js       # Main JavaScript entry
│   ├── pages/       # React page components
│   └── component/   # Reusable React components
├── scss/            # Stylesheets
└── controllers/     # Stimulus controllers

config/
├── packages/        # Bundle configurations
├── routes/          # Routing configuration
└── services.yaml    # Service container config
```

## Testing Strategy

**Current State**: No automated tests present in this demo application.

**For Production**: Would typically include:

- PHPUnit for unit/integration tests
- Symfony's WebTestCase for functional tests
- Jest/React Testing Library for JavaScript tests

## Common Patterns

### Adding a New Route

1. Add route attribute to controller method
2. Create corresponding Twig template
3. Update navigation in `templates/base.html.twig` if needed

### Adding a New React Component

1. Create component in `assets/js/component/`
2. Create page wrapper in `assets/js/pages/`
3. Add entry point in `webpack.config.js`
4. Include in Twig template with `encore_entry_script_tags()`

### Modifying Security Configuration

The 2FA implementation is tightly coupled across three components:

- `src/Security/TwoFactorsAuthenticator.php`
- `src/EventSubscriber/DoubleAuthentificationSubscriber.php`
- `config/packages/security.yaml`

Changes to authentication flow require updates to all three.

## Deployment

**Production deployment** (via CircleCI on main branch):

1. Docker image built with version tags
2. Image pushed to Docker Hub
3. SSH deployment to production server
4. Server pulls new image and restarts containers

**Version management**:

- Docker tag: `1.{CIRCLE_BUILD_NUM}`
- Main branch also tagged as `latest`
- Version displayed in footer of all pages
