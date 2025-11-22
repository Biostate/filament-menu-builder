# GitBook Setup

This documentation is built with GitBook. Here's how to set it up and deploy it.

## Local Development

### Option 1: Using GitBook CLI

1. Install GitBook CLI:
```bash
npm install -g gitbook-cli
```

2. Install GitBook:
```bash
gitbook install
```

3. Serve locally:
```bash
gitbook serve
```

The documentation will be available at `http://localhost:4000`

### Option 2: Using GitBook.com

1. Create an account on [GitBook.com](https://www.gitbook.com)
2. Create a new space
3. Connect it to your GitHub repository
4. Select the `docs` folder as the root
5. GitBook will automatically build and deploy your documentation

## GitHub Pages Deployment

You can also deploy GitBook to GitHub Pages:

1. Build the GitBook:
```bash
gitbook build
```

2. This creates a `_book` directory with the static site
3. Configure GitHub Pages to serve from the `_book` directory

## Structure

- `README.md` - Homepage/introduction
- `SUMMARY.md` - Navigation structure
- `.gitbook.yaml` - GitBook configuration
- `*.md` - Documentation pages

## Navigation

Edit `SUMMARY.md` to modify the navigation structure. The format is:

```markdown
* [Page Title](filename.md)
  * [Subsection](filename.md#anchor)
```

