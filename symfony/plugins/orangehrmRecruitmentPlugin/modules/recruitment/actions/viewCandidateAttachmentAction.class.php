<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewCandidateAttachmentAction
 *
 * @author orangehrm
 */
class viewCandidateAttachmentAction extends sfAction {

    /**
     *
     * @return <type> 
     */
    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }

    /**
     *
     * @return <type> 
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
            $this->vacancyService->setVacancyDao(new VacancyDao());
        }
        return $this->vacancyService;
    }

    /**
     *
     * @return <type> 
     */
    public function getRecruitmentAttachmentService() {
        if (is_null($this->recruitmentAttachmentService)) {
            $this->recruitmentAttachmentService = new RecruitmentAttachmentService();
            $this->recruitmentAttachmentService->setRecruitmentAttachmentDao(new RecruitmentAttachmentDao());
        }
        return $this->recruitmentAttachmentService;
    }

    /**
     *
     * @param <type> $request
     * @return <type> 
     */
    public function execute($request) {

        // this should probably be kept in session?
        $attachId = $request->getParameter('attachId');

        $recruitmentAttachmentService = $this->getRecruitmentAttachmentService();
        $attachment = $recruitmentAttachmentService->getCandidateAttachment($attachId);

        $response = $this->getResponse();

        if (!empty($attachment)) {
            $contents = $attachment->getFileContent();
            $contentType = $attachment->getAttachmentType();
            $fileName = $attachment->getFileName();
            $fileLength = $attachment->getFileSize();

            $response->setHttpHeader('Pragma', 'public');

            $response->setHttpHeader('Expires', '0');
            $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0, max-age=0");
            $response->setHttpHeader("Cache-Control", "private", false);
            $response->setHttpHeader("Content-Type", $contentType);
            $response->setHttpHeader("Content-Disposition", 'attachment; filename="' . $fileName . '";');
            $response->setHttpHeader("Content-Transfer-Encoding", "binary");
            $response->setHttpHeader("Content-Length", $fileLength);

            $response->setContent($contents);
            $response->send();
        } else {
            $response->setStatusCode(404, 'This attachment does not exist');
        }

        return sfView::NONE;
    }

}

