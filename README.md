# GraphQL SDL transcriber

Automatically generates a file containing your GraphQL schema, represented by the
[GraphQL Type System Definition Language](http://spec.graphql.org/June2018/#sec-Type-System). This is mainly intended
for use with the IntelliJ [GraphQL plugin](https://plugins.jetbrains.com/plugin/8097-graphql).

## Install

`composer require bigfork/silverstripe-graphql-sdl`

## Usage

The SDL files are, by default, only built in `dev` mode and are output to `public/_graphql/schemaname.sdl.graphql`. All
schemas are included, but if this causes problems (e.g. conflicting types) it’s possible to only enable this for 
specific schemas via YAML config:

```yml
Bigfork\SilverstripeGraphQLSDL\SDLTranscribeHandler:
  schemas:
    - default
```
