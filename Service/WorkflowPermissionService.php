<?php

namespace Dergipark\WorkflowBundle\Service;

use Dergipark\WorkflowBundle\Entity\ArticleWorkflow;
use Dergipark\WorkflowBundle\Entity\ArticleWorkflowStep;
use Dergipark\WorkflowBundle\Entity\StepDialog;

class WorkflowPermissionService
{
    /**
     * @var WorkflowService
     */
    private $workflowService;

    /**
     * WorkflowPermissionService constructor.
     * @param WorkflowService $workflowService
     */
    public function __construct(
        WorkflowService $workflowService
    ) {
        $this->workflowService  = $workflowService;
    }

    /**
     * @return bool
     */
    public function isHaveEditorRole()
    {
        $user = $this->workflowService->getUser();
        $journal = $this->workflowService->journalService->getSelectedJournal();

        if($user->isAdmin()
            || $this->haveLeastRole(['ROLE_EDITOR', 'ROLE_CO_EDITOR'], $user->getJournalRolesBag($journal))){
            return true;
        }

        return false;
    }

    /**
     * @param ArticleWorkflow $workflow
     * @return bool
     */
    public function isInWorkflowRelatedUsers(ArticleWorkflow $workflow)
    {
        $user = $this->workflowService->getUser();
        $journal = $this->workflowService->journalService->getSelectedJournal();

        if($user->isAdmin()
            || $this->haveLeastRole(['ROLE_EDITOR', 'ROLE_CO_EDITOR'], $user->getJournalRolesBag($journal))){
            return true;
        }
        if($workflow->relatedUsers->contains($user)){
            return true;
        }

        return false;
    }

    /**
     * @param ArticleWorkflowStep $step
     * @return bool
     */
    public function isGrantedForStep(ArticleWorkflowStep $step)
    {
        $user = $this->workflowService->getUser();
        $journal = $this->workflowService->journalService->getSelectedJournal();

        if($user->isAdmin()
            || $this->haveLeastRole(['ROLE_EDITOR', 'ROLE_CO_EDITOR'], $user->getJournalRolesBag($journal))){
            return true;
        }
        if($step->grantedUsers->contains($user)){
            return true;
        }

        return false;
    }

    /**
     * @param StepDialog $dialog
     * @return bool
     */
    public function isGrantedForDialogPost(StepDialog $dialog)
    {
        $user = $this->workflowService->getUser();
        $journal = $this->workflowService->journalService->getSelectedJournal();

        if($user->isAdmin()
            || $this->haveLeastRole(['ROLE_EDITOR', 'ROLE_CO_EDITOR'], $user->getJournalRolesBag($journal))){
            return true;
        }
        if($dialog->getStep()->grantedUsers->contains($user)){
            return true;
        }
        if($dialog->users->contains($user)){
            return true;
        }

        return false;
    }

    /**
     * @param array $searchRoles
     * @param array $roleBag
     * @return bool
     */
    public function haveLeastRole($searchRoles = [], $roleBag = [])
    {
        if(count(array_intersect($searchRoles, $roleBag)) > 0){
            return true;
        }

        return false;
    }
}