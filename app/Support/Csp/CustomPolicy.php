<?php

namespace App\Support\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policies\Basic;

class CustomPolicy extends Basic
{
    public function configure()
    {
        parent::configure();

        $this
            // Frontend specific setups for Vue/Inertia/Vite
            ->addDirective(Directive::CONNECT, Keyword::SELF)
            ->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            
            // Allow image blobs for html2canvas to function
            ->addDirective(Directive::IMG, [Keyword::SELF, 'data:', 'blob:'])
            
            // Allow media/fonts
            ->addDirective(Directive::MEDIA, Keyword::SELF)
            ->addDirective(Directive::OBJECT, Keyword::NONE)
            ->addDirective(Directive::FONT, [Keyword::SELF, 'https://fonts.gstatic.com'])

            // Script handling (Vue requires some leeway in dev, but build is strict)
            ->addDirective(Directive::SCRIPT, [Keyword::SELF])
            
            // Styles
            ->addDirective(Directive::STYLE, [Keyword::SELF, Keyword::UNSAFE_INLINE, 'https://fonts.googleapis.com'])

            // If using Vite dev server
            ->addDirective(Directive::CONNECT, ['ws://localhost:5173', 'http://localhost:5173', 'ws://personaility.test:5173'])
            ->addDirective(Directive::SCRIPT, ['http://localhost:5173', 'http://personaility.test:5173']);
    }
}
