# lil-pins

This project uses [`HTMLButtonElement command`](https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/command) (2025)
and [`CSS nesting`](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Selectors/Nesting_selector) (2023),
so you will need an updated browser. (I'm sorry, my retro peers.)

I made this project to replace my instance of [Pinry](https://github.com/pinry/pinry) because, I was changing servers
and needed something that could use SQLite and be run easily without Docker.

## How To Use

**Don't** upload `forgot_login.php` to your public webserver.
Only use this file if you forget your credentials.
Preferably you'd use this file with your database on a local server.
If you do need to upload it to your public server, **delete** it immediately after use.

### "I don't like the colors."
:( You can change them in `src/style.css`.
The following are all the colors used from darkest to lightest.

```css
:root {
    --darker    : #2E2530;
    --darker-o  : #2E253066; /* Dialogue overlay backdrop */
    --dark      : #5A4A5C;
    --medium    : #8C7B8E;
    --light     : #C2B8C4;
    --lighter   : #EFECEF;
}
```

## Licenses

This project, **lil-pins**, is licensed under MIT.

The font included, Averia Libre, is by Dan Sayers and is licensed under [OFL 1.1](https://openfontlicense.org/).
