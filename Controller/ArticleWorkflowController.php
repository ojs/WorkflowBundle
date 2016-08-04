<?php

namespace Dergipark\WorkflowBundle\Controller;

use Dergipark\WorkflowBundle\Entity\ArticleWorkflow;
use Dergipark\WorkflowBundle\Entity\ArticleWorkflowStep;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Journal;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ArticleWorkflowController extends Controller
{
    /**
     * @param $workflowId
     * @return Response
     */
    public function timelineAction($workflowId)
    {
        $workflowService = $this->get('dp.workflow_service');
        $workflow = $workflowService->getArticleWorkflow($workflowId);
        $this->throw404IfNotFound($workflow);

        if(!$this->get('dp.workflow_permission_service')->isGrantedForTimeline($workflow)){
            throw new AccessDeniedException;
        }
        return $this->render('DergiparkWorkflowBundle:ArticleWorkflow:_timeline.html.twig', [
            'timeline' => $workflowService->getWorkflowTimeline($workflow),
        ]);
    }

    /**
     * @param $workflowId
     * @param $stepOrder
     * @return Response
     */
    public function stepAction($workflowId, $stepOrder)
    {
        $em = $this->getDoctrine()->getManager();
        $workflowService = $this->get('dp.workflow_service');
        $workflow = $workflowService->getArticleWorkflow($workflowId);
        $step = $em->getRepository(ArticleWorkflowStep::class)->findOneBy([
            'articleWorkflow' => $workflow,
            'order' => $stepOrder,
        ]);
        if(!$this->get('dp.workflow_permission_service')->isGrantedForTimeline($workflow)){
            throw new AccessDeniedException;
        }

        return $this->render('DergiparkWorkflowBundle:ArticleWorkflow:steps/_step_'.$stepOrder.'.html.twig', [
            'step' => $step,
        ]);
    }

    /**
     * @param $workflowId
     * @return Response
     */
    public function historyLogAction($workflowId)
    {
        if(!$this->get('dp.workflow_permission_service')->isGrantedForWorkflowHistory()){
            throw new AccessDeniedException;
        }
        $workflowService = $this->get('dp.workflow_service');
        $workflow = $workflowService->getArticleWorkflow($workflowId);

        return $this->render('DergiparkWorkflowBundle:ArticleWorkflow:_history_log.html.twig', [
            'logs' => $workflowService->getWorkflowLogs($workflow),
        ]);
    }
}
