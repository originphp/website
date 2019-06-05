@extends('_layouts.master')

@section('body')
<section class="container max-w-2xl mx-auto px-6 py-10 md:py-12">
    <div class="flex flex-col-reverse mb-10 lg:flex-row lg:mb-24">
        <div class="mt-8">
            <h1 id="intro-docs-template">{{ $page->siteName }}</h1>

            <h2 id="intro-powered-by-jigsaw" class="font-light mt-4">{{ $page->siteDescription }}</h2>

            <!--p class="text-lg">Give your documentation a boost with Jigsaw. <br class="hidden sm:block">Generate elegant, static docs quickly and easily.</p-->

            <div class="flex my-10">
                <a href="/docs/getting-started" title="{{ $page->siteName }} getting started" class="bg-blue hover:bg-blue-dark font-normal text-white hover:text-white rounded mr-4 py-2 px-6">Get Started</a>

                <a href="https://github.com/originphp/framework" title="OriginPHP Source Code" class="bg-grey-light hover:bg-grey-dark text-blue-darkest font-normal hover:text-white rounded py-2 px-6">Source Code</a>
            </div>
        </div>

        <img src="/assets/img/logo-large.svg" alt="{{ $page->siteName }} large logo" class="mx-auto mb-6 lg:mb-0 ">
    </div>

    <hr class="block my-8 border lg:hidden">

    <div class="md:flex -mx-2 -mx-4">
        <div class="mb-8 mx-3 px-2 md:w-1/3">

            <h3 id="intro-laravel" class="text-2xl text-blue-darkest mb-0"><i class="fas fa-shipping-fast"></i> Rapid Development</h3>

            <p>Build quickly high performance scalable applications using a MVC (model view controller) design pattern, code generation utilities and minimal configuration using magic.</p>
        </div>

        <div class="mb-8 mx-3 px-2 md:w-1/3">
            <h3 id="intro-markdown" class="text-2xl text-blue-darkest mb-0"><i class="fas fa-child"></i> Easy to Use</h3>

            <p>The framework is designed to be easy to use by any developer, whether starter or professional following logical patterns and structures with things that just make sense.</p>
        </div>

        <div class="mx-3 px-2 md:w-1/3">
            <h3 id="intro-mix" class="text-2xl text-blue-darkest mb-0"><i class="fas fa-layer-group"></i> Packed with Features</h3>

            <p>OriginPHP includes Cache, Queue, Email,Console Applications, Middleware, Events, Xml and Yaml reader/writers, debugging, dockerized development environment, integration testing and much more.</p>
        </div>
    </div>
</section>
@endsection
