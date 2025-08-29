<div class="max-w-4xl mx-auto px-4">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            @switch(app()->getLocale())
                @case('en')
                    Welcome to EduSpark
                    @break
                @case('hu') 
                    Üdvözöljük az EduSparkban
                    @break
                @default
                    Добро дошли у EduSpark
            @endswitch
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            @switch(app()->getLocale())
                @case('en')
                    Educational quizzes for primary school students (grades 1-8)
                    @break
                @case('hu')
                    Oktatási kvízek általános iskolai tanulók számára (1-8. osztály)
                    @break
                @default
                    Образовни квизови за ученике основне школе (1-8. разред)
            @endswitch
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/start" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg text-lg transition duration-200">
                @switch(app()->getLocale())
                    @case('en')
                        Start Quiz
                        @break
                    @case('hu')
                        Kvíz indítása
                        @break
                    @default
                        Започни квиз
                @endswitch
            </a>
            
            @guest
                <a href="/login" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg text-lg transition duration-200">
                    @switch(app()->getLocale())
                        @case('en')
                            Sign In
                            @break
                        @case('hu')
                            Bejelentkezés
                            @break
                        @default
                            Пријави се
                    @endswitch
                </a>
            @endguest
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">
            @switch(app()->getLocale())
                @case('en')
                    Available Subjects
                    @break
                @case('hu')
                    Elérhető tantárgyak
                    @break
                @default
                    Доступни предмети
            @endswitch
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($subjects as $subject)
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <h3 class="font-semibold text-gray-900">
                        {{ $subject->getName(app()->getLocale()) }}
                    </h3>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-12 text-center">
        <div class="bg-blue-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-blue-900 mb-4">
                @switch(app()->getLocale())
                    @case('en')
                        For Teachers
                        @break
                    @case('hu')
                        Tanároknak
                        @break
                    @default
                        За наставнике
                @endswitch
            </h2>
            <p class="text-blue-700 mb-4">
                @switch(app()->getLocale())
                    @case('en')
                        Manage quizzes, questions, and track student progress
                        @break
                    @case('hu')
                        Kvízek és kérdések kezelése, tanulói előrehaladás nyomon követése
                        @break
                    @default
                        Управљајте квизовима, питањима и пратите напредак ученика
                @endswitch
            </p>
            <a href="/teacher" class="text-blue-600 hover:text-blue-800 font-medium">
                @switch(app()->getLocale())
                    @case('en')
                        Teacher Panel →
                        @break
                    @case('hu')
                        Tanári panel →
                        @break
                    @default
                        Наставнички панел →
                @endswitch
            </a>
        </div>
    </div>
</div>