<?php declare(strict_types=1);

namespace Shopware\Storefront\Page\Newsletter\Subscribe;

use Shopware\Core\Content\Category\Exception\CategoryNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class NewsletterSubscribePageLoader
{
    /**
     * @var GenericPageLoader
     */
    private $genericLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        GenericPageLoader $genericLoader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->genericLoader = $genericLoader;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws CategoryNotFoundException
     * @throws InconsistentCriteriaIdsException
     * @throws MissingRequestParameterException
     */
    public function load(Request $request, SalesChannelContext $salesChannelContext): NewsletterSubscribePage
    {
        $page = $this->genericLoader->load($request, $salesChannelContext);
        $page = NewsletterSubscribePage::createFrom($page);

        $this->eventDispatcher->dispatch(
            new NewsletterSubscribePageLoadedEvent($page, $salesChannelContext, $request),
            NewsletterSubscribePageLoadedEvent::NAME
        );

        return $page;
    }
}
