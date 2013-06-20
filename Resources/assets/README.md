SETTING THINGS UP
=================
1) Define Foundation version in Gemfile

    source :rubygems

    gem "zurb-foundation", "3.2.4"

2) Run bundle install:

    $ bundle install

3) Install Foundation assets:

    WARNING! THIS WILL OVERRIDE scss/app.scss AND scss/_settings.scss

    $ bundle exec compass install foundation --force

4) Run compass watch:

    $ bundle exec compass watch

5) Configure assetic:

    {% stylesheets
        "@AcmeCoreBundle/Resources/assets/css/app.css"
    %}
        <link rel="stylesheet" href="{{ asset_url }}" />
    {% endstylesheets %}

    {% javascripts
        "@AcmeCoreBundle/Resources/assets/js/vendor/foundation/modernizr.foundation.js"
        ...
        "@AcmeCoreBundle/Resources/assets/js/app.js"
    %}
        <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}