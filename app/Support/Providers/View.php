<?php namespace App\Support\Providers;

use Naraki\System\Models\SystemPageView as ViewModel;
use App\Support\View\ViewSessionHistory;
use App\Support\View\VisitorCookieRepository;
use Carbon\Carbon;
use CyrildeWit\EloquentViewable\Contracts\CrawlerDetector;
use CyrildeWit\EloquentViewable\Contracts\HeaderResolver;
use CyrildeWit\EloquentViewable\Contracts\IpAddressResolver;
use CyrildeWit\EloquentViewable\Contracts\View as ViewContract;
use CyrildeWit\EloquentViewable\Contracts\Viewable as ViewableContract;
use CyrildeWit\EloquentViewable\Support\Key;
use CyrildeWit\EloquentViewable\Support\Period;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class View extends Model
{
    protected $model = ViewModel::class;
    /**
     * The viewable model where we are applying actions to.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $viewable;

    /**
     * The period that the current query should scoped to.
     *
     * @var \CyrildeWit\EloquentViewable\Support\Period|null
     */
    protected $period = null;

    /**
     * Determine if only unique views should be returned.
     *
     * @var bool
     */
    protected $unique = false;

    /**
     * The delay that should be finished before a new view can be recorded.
     *
     * @var \DateTime|null
     */
    protected $sessionDelay = null;

    /**
     * The collection under where the view will be saved.
     *
     * @var string|null
     */
    public $collection = null;

    /**
     * Determine if the views count should be cached.
     *
     * @var string|null
     */
    protected $shouldCache = false;

    /**
     * Determine if the views count should be cached.
     *
     * @var \DateTime|null
     */
    public $cacheLifetime = false;

    /**
     * Used IP Address instead of the provided one by the resolver.
     *
     * @var string
     */
    protected $overriddenIpAddress;

    /**
     * Used visitor ID instead of the provided one by a cookie.
     *
     * @var string
     */
    protected $overriddenVisitor;

    /**
     * The view session history instance.
     *
     * @var \CyrildeWit\EloquentViewable\ViewSessionHistory
     */
    protected $viewSessionHistory;

    /**
     * The visitor cookie repository instance.
     *
     * @var \App\Support\View\VisitorCookieRepository
     */
    protected $visitorCookieRepository;

    /**
     * The crawler detector instance.
     *
     * @var \CyrildeWit\EloquentViewable\Contracts\CrawlerDetector
     */
    protected $crawlerDetector;

    /**
     * The IP Address resolver instance.
     *
     * @var \CyrildeWit\EloquentViewable\Contracts\IpAddressResolver
     */
    protected $ipAddressResolver;

    /**
     * The request header resolver instance.
     *
     * @var \CyrildeWit\EloquentViewable\Contracts\HeaderResolver
     */
    protected $headerResolver;

    /**
     * The cache repository instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new views instance.
     *
     * @param \App\Support\View\ViewSessionHistory $viewSessionHistory
     * @param \App\Support\View\VisitorCookieRepository $visitorCookieRepository
     * @param \CyrildeWit\EloquentViewable\Contracts\CrawlerDetector $crawlerDetector
     * @param \CyrildeWit\EloquentViewable\Contracts\IpAddressResolver $ipAddressResolver
     * @param \CyrildeWit\EloquentViewable\Contracts\HeaderResolver $headerResolver
     * @param \Illuminate\Contracts\Cache\Repository $cache
     */
    public function __construct(
        ViewSessionHistory $viewSessionHistory,
        VisitorCookieRepository $visitorCookieRepository,
        CrawlerDetector $crawlerDetector,
        IpAddressResolver $ipAddressResolver,
        HeaderResolver $headerResolver,
        CacheRepository $cache
    ) {
        parent::__construct();
        $this->viewSessionHistory = $viewSessionHistory;
        $this->visitorCookieRepository = $visitorCookieRepository;
        $this->crawlerDetector = $crawlerDetector;
        $this->ipAddressResolver = $ipAddressResolver;
        $this->headerResolver = $headerResolver;
        $this->cache = $cache;
        $this->cacheLifetime = Carbon::now()->addSeconds(config('eloquent-viewable.cache.lifetime_in_seconds'));
    }

    /**
     * Save a new record of the made view.
     *
     * @return bool
     */
    public function record(): bool
    {
        if ($this->shouldRecord()) {
            $model = $this->createModel([
                'entity_type_id' => $this->viewable->getAttribute('entity_type_id'),
                'visitor' => $this->resolveVisitorId(),
                'viewed_at' => Carbon::now()
            ]);
            return $model->save();
        }

        return false;
    }

    /**
     * Count the views for a viewable type.
     *
     * @param  string|  $viewableType
     * @return int
     */
    public function countByType($viewableType): int
    {
        if ($viewableType instanceof ViewableContract) {
            $viewableType = $viewableType->getMorphClass();
        }

        $cacheKey = Key::createForType($viewableType, $this->period ?? Period::create(), $this->unique);

        if ($this->shouldCache) {
            $cachedViewsCount = $this->cache->get($cacheKey);

            if ($cachedViewsCount !== null) {
                return (int)$cachedViewsCount;
            }
        }

        $query = app(ViewContract::class)->where('viewable_type', $viewableType);

        if ($period = $this->period) {
            $query->withinPeriod($period);
        }

        if ($this->unique) {
            $viewsCount = $query->uniqueVisitor()->count('visitor');
        } else {
            $viewsCount = $query->count();
        }

        if ($this->shouldCache) {
            $this->cache->put($cacheKey, $viewsCount, $this->cacheLifetime);
        }

        return $viewsCount;
    }

    /**
     * Count the views.
     *
     * @return int
     */
    public function count(): int
    {
        $query = $this->viewable->views();

        $cacheKey = Key::createForEntity($this->viewable, $this->period ?? Period::create(), $this->unique,
            $this->collection);

        if ($this->shouldCache) {
            $cachedViewsCount = $this->cache->get($cacheKey);

            if ($cachedViewsCount !== null) {
                return (int)$cachedViewsCount;
            }
        }

        if ($period = $this->period) {
            $query->withinPeriod($period);
        }

        $query->where('collection', $this->collection);

        if ($this->unique) {
            $viewsCount = $query->uniqueVisitor()->count('visitor');
        } else {
            $viewsCount = $query->count();
        }

        if ($this->shouldCache) {
            $this->cache->put($cacheKey, $viewsCount, $this->cacheLifetime);
        }

        return $viewsCount;
    }

    /**
     * Destroy all views of the viewable model.
     *
     * @return void
     */
    public function destroy()
    {
        $this->viewable->views()->delete();
    }

    /**
     * Set the viewable model.
     *
     * @param  \CyrildeWit\EloquentViewable\Contracts\Viewable|null
     * @return self
     */
    public function forViewable($viewable = null): self
    {
        $this->viewable = $viewable;

        return $this;
    }

    /**
     * Set the delay in the session.
     *
     * @param  \DateTime|int $delay
     * @return self
     */
    public function delayInSession($delay): self
    {
        if (is_int($delay)) {
            $delay = Carbon::now()->addSeconds($delay);
        }

        $this->sessionDelay = $delay;

        return $this;
    }

    /**
     * Set the period.
     *
     * @param  \CyrildeWit\EloquentViewable\Period
     * @return self
     */
    public function period($period): self
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Set the collection.
     *
     * @param  string
     * @return self
     */
    public function collection(string $name): self
    {
        $this->collection = $name;

        return $this;
    }

    /**
     * Fetch only unique views.
     *
     * @param  bool $state
     * @return self
     */
    public function unique(bool $state = true): self
    {
        $this->unique = $state;

        return $this;
    }

    /**
     * Cache the current views count.
     *
     * @param  \DateTime|int $lifetime
     * @return self
     */
    public function remember($lifetime = null)
    {
        $this->shouldCache = true;
        $this->cacheLifetiem = $lifetime;

        return $this;
    }

    /**
     * Override the visitor's IP Address.
     *
     * @param  string $address
     * @return self
     */
    public function overrideIpAddress(string $address)
    {
        $this->overriddenIpAddress = $address;

        return $this;
    }

    /**
     * Override the visitor's unique ID.
     *
     * @param  string $visitor
     * @return self
     */
    public function overrideVisitor(string $visitor)
    {
        $this->overriddenVisitor = $visitor;

        return $this;
    }

    /**
     * Determine if we should record the view.
     *
     * @return bool
     */
    protected function shouldRecord(): bool
    {
        // If ignore bots is true and the current viewer is a bot, return false
        if (config('eloquent-viewable.ignore_bots') && $this->crawlerDetector->isCrawler()) {
            return false;
        }

        // If we honor to the DNT header and the current request contains the
        // DNT header, return false
        if (config('eloquent-viewable.honor_dnt', false) && $this->requestHasDoNotTrackHeader()) {
            return false;
        }

        if (collect(config('eloquent-viewable.ignored_ip_addresses'))->contains($this->resolveIpAddress())) {
            return false;
        }

        if (!is_null($this->sessionDelay) && !$this->viewSessionHistory->push($this->viewable, $this->sessionDelay)) {
            return false;
        }

        return true;
    }

    /**
     * Resolve the visitor's IP Address.
     *
     * It will first check if the overriddenIpAddress property has been set,
     * otherwise it will resolve it using the IP Address resolver.
     *
     * @return string
     */
    protected function resolveIpAddress(): string
    {
        return $this->overriddenIpAddress ?? $this->ipAddressResolver->resolve();
    }

    /**
     * Determine if the request has a Do Not Track header.
     *
     * @return string
     */
    protected function requestHasDoNotTrackHeader(): bool
    {
        return 1 === (int)$this->headerResolver->resolve('HTTP_DNT');
    }

    /**
     * Resolve the visitor's unique ID.
     *
     * @return string|null
     */
    protected function resolveVisitorId()
    {
        return $this->overriddenVisitor ?? $this->visitorCookieRepository->get();
    }

}