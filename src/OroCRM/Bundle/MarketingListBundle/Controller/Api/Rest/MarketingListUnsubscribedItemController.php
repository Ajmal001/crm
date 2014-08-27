<?php

namespace OroCRM\Bundle\MarketingListBundle\Controller\Api\Rest;

use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Oro\Bundle\SecurityBundle\Exception\ForbiddenException;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\Rest\Util\Codes;

use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListUnsubscribedItem;

/**
 * @Rest\RouteResource("marketinglist_unsubscribeditem")
 * @Rest\NamePrefix("orocrm_api_")
 */
class MarketingListUnsubscribedItemController extends RestController implements ClassResourceInterface
{
    /**
     * REST POST
     *
     * @ApiDoc(
     *     description="Create new MarketingListUnsubscribedItem",
     *     resource=true
     * )
     * @AclAncestor("orocrm_marketinglist_unsubscribed_item_create")
     */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * REST DELETE
     *
     * @param int $id
     *
     * @ApiDoc(
     *     description="Delete MarketingListRemovedItem",
     *     resource=true
     * )
     * @AclAncestor("orocrm_marketinglist_unsubscribed_item_delete")
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Unsubscribe marketing list entity item
     *
     * Returns
     * - HTTP_OK (200)
     *
     * @Rest\Get(
     *      "/marketinglist/{marketingList}/unsubscribe/{id}"
     * )
     * @ApiDoc(description="Unsubscribe marketing list entity item", resource=true)
     * @AclAncestor("orocrm_marketinglist_unsubscribed_item_create")
     *
     * @param MarketingList $marketingList
     * @param int $id
     * @return Response
     */
    public function unsubscribeAction(MarketingList $marketingList, $id)
    {
        $item = new MarketingListUnsubscribedItem();
        $item->setMarketingList($marketingList)
            ->setEntityId($id);
        $em = $this->getManager()->getObjectManager();
        $em->persist($item);
        $em->flush($item);

        return $this->handleView(
            $this->view(
                array(
                    'successful' => true,
                    'message' => $this->get('translator')->trans('orocrm.marketinglist.controller.unsubscribed')
                ),
                Codes::HTTP_OK
            )
        );
    }

    /**
     * @param MarketingList $marketingList
     * @param int $id
     *
     * @Rest\Get(
     *      "/marketinglist/{marketingList}/subscribe/{id}"
     * )
     * @ApiDoc(
     *     description="Delete MarketingListUnsubscribedItem by marketing list entity",
     *     resource=true
     * )
     * @AclAncestor("orocrm_marketinglist_unsubscribed_item_delete")
     *
     * @return Response
     */
    public function subscribeAction(MarketingList $marketingList, $id)
    {
        /** @var MarketingListUnsubscribedItem[] $forRemove */
        $forRemove = $this->getManager()->getRepository()->findBy(
            array(
                'marketingList' => $marketingList,
                'entityId' => $id
            )
        );
        if ($forRemove) {
            try {
                $item = $forRemove[0];
                $this->getDeleteHandler()->handleDelete($item->getId(), $this->getManager());
            } catch (EntityNotFoundException $notFoundEx) {
                return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
            } catch (ForbiddenException $forbiddenEx) {
                return $this->handleView(
                    $this->view(['reason' => $forbiddenEx->getReason()], Codes::HTTP_FORBIDDEN)
                );
            }
        }

        return $this->handleView(
            $this->view(
                array(
                    'successful' => true,
                    'message' => $this->get('translator')->trans('orocrm.marketinglist.controller.subscribed')
                ),
                Codes::HTTP_OK
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->get('orocrm_marketing_list.marketing_list_unsubscribed_item.manager.api');
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->get('orocrm_marketing_list.form.marketing_list_unsubscribed_item');
    }

    /**
     * {@inheritdoc}
     */
    public function getFormHandler()
    {
        return $this->get('orocrm_marketing_list.form.handler.marketing_list_unsubscribed_item');
    }
}
