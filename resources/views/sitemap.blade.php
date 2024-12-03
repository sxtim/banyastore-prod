<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($categories as $category)
        <url>
            <loc>https://banyastore.ru/{{ $category->slug }}</loc>
            <lastmod>{{ $date }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
    @foreach($products as $product)
        <url>
            <loc>https://banyastore.ru/{{ $product->slug }}</loc>
            <lastmod>{{ $date }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
    @foreach($news as $item)
        <url>
            <loc>https://banyastore.ru/{{ $item->slug }}</loc>
            <lastmod>{{ $date }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.3</priority>
        </url>
    @endforeach
    @foreach($actions as $item)
        <url>
            <loc>https://banyastore.ru/{{ $item->slug }}</loc>
            <lastmod>{{ $date }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.3</priority>
        </url>
    @endforeach
    <url>
        <loc>https://banyastore.ru/actions</loc>
        <lastmod>{{ $date }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>https://banyastore.ru/news</loc>
        <lastmod>{{ $date }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>https://banyastore.ru/company</loc>
        <lastmod>{{ $date }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>https://banyastore.ru/three-d-projects</loc>
        <lastmod>{{ $date }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>https://banyastore.ru/feedback</loc>
        <lastmod>{{ $date }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.3</priority>
    </url>
</urlset>
