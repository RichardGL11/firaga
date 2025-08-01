@php
    use App\View\TemplateRenderer;
    $builder = TemplateRenderer::make();
@endphp

@props([
    'post' => null,
])

<article class="pb-8 md:pb-0 bg-elevation-surface">
    <section id="heading" class="bg-elevation-02dp container mx-auto">
        <div class="max-w-4xl mx-auto pt-10">
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center gap-2 text-text-light hover:text-brand-primary-hover mb-8">
                <x-heroicon-c-arrow-left class="h-4 w-4"/>
                Voltar ao Blog
            </a>
            <div class="relative mb-12 md:max-h-[300px] max-h-[150px] rounded-xl overflow-hidden">
                <img
                    alt="{{ $post->title }}" loading="lazy" decoding="async"
                    class="object-cover"
                    src="{{ $post->getFirstMediaUrl('cover') }}">
            </div>
            <div class="flex gap-2 mb-2">
                @foreach($post->categories as $category)
                    <x-layout.shared.chip variant="custom"
                        class="px-3 py-1 text-sm bg-brand-primary/40 hover:bg-brand-primary/90 text-white group-hover:bg-icon-high group-hover:text-brand-primary">
                        {{ $category->name ?? 'Code' }}
                    </x-layout.shared.chip>
                @endforeach
            </div>
            <h1 class="text-3xl text-text-high font-bold mb-6 md:text-4xl lg:text-5xl">
                {{ $post->title }}
            </h1>
            <div
                class="flex flex-wrap justify-between  items-center gap-6 text-text-high mb-8 pb-8 ">
                <div class="flex gap-6">
                    <div class="flex items-center gap-2">
                        <x-heroicon-c-calendar class="h-4 w-4"/>
                        {{ $post->published_at->format('d/m/Y') }}
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-c-clock class="h-3 w-3"/>
                        {{ $post->read_time_in_minutes }} minutos de leitura
                    </div>
                </div>
                <div class="flex items-center gap-2 mr-4">
                    <div class="flex items-center">
                        <img alt="{{ $post->author->name ?? '' }}" loading="lazy"
                             decoding="async" data-nimg="1"
                             class="rounded-full w-8 h-8"
                             src="{{ $post->author->getFirstMediaUrl('avatar') }}">
                    </div>
                    <span class="">Por {{ $post->author->name }}</span>
                </div>
            </div>
        </div>
    </section>

    <section id="content" class="container mx-auto px-4 md:px-8">
        <article class="prose lg:prose-xl max-w-none dark:prose-invert">
            @foreach($post->content as $componentPayload)
                {!! $builder->render($post, $componentPayload['type'], $componentPayload['data']) !!}
            @endforeach
        </article>
    </section>
    <section id="author" class="container mx-auto mt-12 pb-10">
        <div class="max-w-4xl mx-auto mt-12 p-6 bg-elevation-02dp rounded-xl flex items-center gap-6">
            <img
                alt="{{ $post->author->name ?? '' }}"
                loading="lazy"
                decoding="async"
                class="rounded-full w-16 h-16 border-2 border-outline-light"
                src="{{ $post->author->getFirstMediaUrl('avatar') }}"
            >
            <div class="flex flex-col">
                <h2 class="text-lg font-bold text-text-high">Autor</h2>
                <div class="flex flex-col">
                    <p class="text-text-high font-semibold">
                        {{ $post->author->name }}
                    </p>
                    <p class="text-text-medium">
                        Consultor Financeiro
                    </p>
                </div>
                <p class="text-sm text-text-medium mt-1">{{ $post->author->description }}</p>
            </div>
        </div>
    </section>
</article>
