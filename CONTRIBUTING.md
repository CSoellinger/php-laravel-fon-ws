# Contributing

Contributions are welcome and will be fully credited!

We accept contributions via Pull Requests on [GitHub](https://github.com/csoellinger/php-laravel-fon-ws).

## Pull Requests

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](https://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your main branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](https://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Running Tests

```bash
composer test
```

## Code Quality

We use several tools to ensure code quality:

### Formatting

```bash
composer format
```

### Static Analysis

```bash
composer analyse
```

### Full Check

Before submitting a PR, please run:

```bash
composer format
composer analyse
composer test
```

## Coding Standards

- Follow PSR-12 coding standards
- Use strict types declaration
- Add type hints for all parameters and return types
- Write PHPDoc blocks for complex methods
- Keep methods focused and small

## Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line

## Development Setup

1. Fork the repository
2. Clone your fork
3. Install dependencies: `composer install`
4. Create a feature branch: `git checkout -b feature/my-new-feature`
5. Make your changes
6. Run tests and code quality checks
7. Commit your changes: `git commit -am 'Add some feature'`
8. Push to the branch: `git push origin feature/my-new-feature`
9. Submit a pull request

## Reporting Bugs

Please report bugs via the [GitHub issue tracker](https://github.com/csoellinger/php-laravel-fon-ws/issues).

When reporting a bug, please include:

- Your Laravel version
- Your PHP version
- Detailed steps to reproduce
- What you expected to happen
- What actually happened
- Any relevant code samples

## Security Vulnerabilities

**DO NOT** report security vulnerabilities through public GitHub issues.

Instead, please send an email to christian.soellinger@gmail.com.

Thank you for contributing!
