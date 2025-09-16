@foreach($cookies->getCategories() as $category)
    <h3 class="h6 mt-3">{{ $category->title }}</h3>

    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
            <tr>
                <th>@lang('cookieConsent::cookies.cookie')</th>
                <th>@lang('cookieConsent::cookies.purpose')</th>
                <th>@lang('cookieConsent::cookies.duration')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($category->getCookies() as $cookie)
                <tr>
                    <td class="fw-medium">{{ $cookie->name }}</td>
                    <td>{{ $cookie->description }}</td>
                    <td>{{ \Carbon\CarbonInterval::minutes($cookie->duration)->cascade() }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endforeach
