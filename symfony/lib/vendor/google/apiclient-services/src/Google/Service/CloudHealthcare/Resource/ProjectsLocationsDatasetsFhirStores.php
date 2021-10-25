<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * The "fhirStores" collection of methods.
 * Typical usage is:
 *  <code>
 *   $healthcareService = new Google_Service_CloudHealthcare(...);
 *   $fhirStores = $healthcareService->fhirStores;
 *  </code>
 */
class Google_Service_CloudHealthcare_Resource_ProjectsLocationsDatasetsFhirStores extends Google_Service_Resource
{
  /**
   * Gets the FHIR [capability statement](http://hl7.org/implement/standards/fhir/
   * STU3/capabilitystatement.html) for the store, which contains a description of
   * functionality supported by the server.
   *
   * Implements the FHIR standard [capabilities interaction](http://hl7.org/implem
   * ent/standards/fhir/STU3/http.html#capabilities).
   *
   * On success, the response body will contain a JSON-encoded representation of a
   * `CapabilityStatement` resource. (fhirStores.capabilities)
   *
   * @param string $name Name of the FHIR store to retrieve the capabilities for.
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudHealthcare_HttpBody
   */
  public function capabilities($name, $optParams = array())
  {
    $params = array('name' => $name);
    $params = array_merge($params, $optParams);
    return $this->call('capabilities', array($params), "Google_Service_CloudHealthcare_HttpBody");
  }
  /**
   * Creates a new FHIR store within the parent dataset. (fhirStores.create)
   *
   * @param string $parent The name of the dataset this FHIR store belongs to.
   * @param Google_Service_CloudHealthcare_FhirStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fhirStoreId The ID of the FHIR store that is being created.
   * The string must match the following regex: `[\p{L}\p{N}_\-\.]{1,256}`.
   * @return Google_Service_CloudHealthcare_FhirStore
   */
  public function create($parent, Google_Service_CloudHealthcare_FhirStore $postBody, $optParams = array())
  {
    $params = array('parent' => $parent, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('create', array($params), "Google_Service_CloudHealthcare_FhirStore");
  }
  /**
   * Deletes the specified FHIR store and removes all resources within it.
   * (fhirStores.delete)
   *
   * @param string $name The resource name of the FHIR store to delete.
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudHealthcare_HealthcareEmpty
   */
  public function delete($name, $optParams = array())
  {
    $params = array('name' => $name);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params), "Google_Service_CloudHealthcare_HealthcareEmpty");
  }
  /**
   * Export resources from the FHIR store to the specified destination.
   *
   * This method returns an Operation that can be used to track the status of the
   * export by calling GetOperation.
   *
   * Immediate fatal errors appear in the error field. Otherwise, when the
   * operation finishes, a detailed response of type ExportResourcesResponse is
   * returned in the response field. The metadata field type for this operation is
   * OperationMetadata. (fhirStores.export)
   *
   * @param string $name The name of the FHIR store to export resource from. The
   * name should be in the format of `projects/{project_id}/locations/{location_id
   * }/datasets/{dataset_id}/fhirStores/{fhir_store_id}`.
   * @param Google_Service_CloudHealthcare_ExportResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudHealthcare_Operation
   */
  public function export($name, Google_Service_CloudHealthcare_ExportResourcesRequest $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('export', array($params), "Google_Service_CloudHealthcare_Operation");
  }
  /**
   * Gets the configuration of the specified FHIR store. (fhirStores.get)
   *
   * @param string $name The resource name of the FHIR store to get.
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudHealthcare_FhirStore
   */
  public function get($name, $optParams = array())
  {
    $params = array('name' => $name);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Google_Service_CloudHealthcare_FhirStore");
  }
  /**
   * Gets the access control policy for a FHIR store or security label within a
   * FHIR store. Returns NOT_FOUND error if the resource does not exist. Returns
   * an empty policy if the resource exists but does not have a policy set.
   *
   * Authorization requires the Google IAM permission
   * 'healthcare.fhirStores.getIamPolicy' for a FHIR store or
   * 'healthcare.securityLabels.getIamPolicy' for a security label
   * (fhirStores.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See the operation documentation for the appropriate value for this
   * field.
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudHealthcare_Policy
   */
  public function getIamPolicy($resource, $optParams = array())
  {
    $params = array('resource' => $resource);
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', array($params), "Google_Service_CloudHealthcare_Policy");
  }
  /**
   * Import resources to the FHIR store by loading data from the specified
   * sources. Each resource must have a client-supplied ID, which is retained by
   * the server.
   *
   * The import operation is idempotent. Upon retry, the most recent data
   * (matching the client-supplied ID) is overwritten, without creating a new
   * resource version. If partial failures occur during the import, successful
   * changes are not rolled back.
   *
   * If history imports are enabled (enable_history_import is set in the FHIR
   * store's configuration), you can import historical versions of a resource by
   * supplying a bundle of type `history`. The historical versions in the bundle
   * must have `lastUpdated` timestamps. If a current or historical version with
   * the supplied resource ID already exists, the bundle is rejected.
   *
   * This method returns an Operation that can be used to track the status of the
   * import by calling GetOperation.
   *
   * Immediate fatal errors appear in the error field. Otherwise, when the
   * operation finishes, a detailed response of type ImportResourcesResponse is
   * returned in the response field. The metadata field type for this operation is
   * OperationMetadata. (fhirStores.import)
   *
   * @param string $name The name of the FHIR store to import FHIR resources to.
   * The name should be in the format of `projects/{project_id}/locations/{locatio
   * n_id}/datasets/{dataset_id}/fhirStores/{fhir_store_id}`.
   * @param Google_Service_CloudHealthcare_ImportResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudHealthcare_Operation
   */
  public function import($name, Google_Service_CloudHealthcare_ImportResourcesRequest $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('import', array($params), "Google_Service_CloudHealthcare_Operation");
  }
  /**
   * Lists the FHIR stores in the given dataset.
   * (fhirStores.listProjectsLocationsDatasetsFhirStores)
   *
   * @param string $parent Name of the dataset.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken The next_page_token value returned from the
   * previous List request, if any.
   * @opt_param int pageSize Limit on the number of FHIR stores to return in a
   * single response.  If zero the default page size of 100 is used.
   * @opt_param string filter Restricts stores returned to those matching a
   * filter. Syntax:
   * https://cloud.google.com/appengine/docs/standard/python/search/query_strings
   * Only filtering on labels is supported, for example `labels.key=value`.
   * @return Google_Service_CloudHealthcare_ListFhirStoresResponse
   */
  public function listProjectsLocationsDatasetsFhirStores($parent, $optParams = array())
  {
    $params = array('parent' => $parent);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Google_Service_CloudHealthcare_ListFhirStoresResponse");
  }
  /**
   * Updates the configuration of the specified FHIR store. (fhirStores.patch)
   *
   * @param string $name Output only. Resource name of the FHIR store, of the form
   * `projects/{project_id}/datasets/{dataset_id}/fhirStores/{fhir_store_id}`.
   * @param Google_Service_CloudHealthcare_FhirStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The update mask applies to the resource. For the
   * `FieldMask` definition, see https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   * @return Google_Service_CloudHealthcare_FhirStore
   */
  public function patch($name, Google_Service_CloudHealthcare_FhirStore $postBody, $optParams = array())
  {
    $params = array('name' => $name, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('patch', array($params), "Google_Service_CloudHealthcare_FhirStore");
  }
  /**
   * Sets the access control policy for a FHIR store or security label within a
   * FHIR store. Replaces any existing policy.
   *
   * Authorization requires the Google IAM permission
   * 'healthcare.fhirStores.setIamPolicy' for a FHIR store or
   * 'healthcare.securityLabels.setIamPolicy' for a security label
   * (fhirStores.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See the operation documentation for the appropriate value for this
   * field.
   * @param Google_Service_CloudHealthcare_SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudHealthcare_Policy
   */
  public function setIamPolicy($resource, Google_Service_CloudHealthcare_SetIamPolicyRequest $postBody, $optParams = array())
  {
    $params = array('resource' => $resource, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', array($params), "Google_Service_CloudHealthcare_Policy");
  }
  /**
   * Returns permissions that a caller has on the specified resource.  If the
   * resource does not exist, this will return an empty set of permissions, not a
   * NOT_FOUND error.
   *
   * There is no permission required to make this API call.
   * (fhirStores.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See the operation documentation for the appropriate value
   * for this field.
   * @param Google_Service_CloudHealthcare_TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_CloudHealthcare_TestIamPermissionsResponse
   */
  public function testIamPermissions($resource, Google_Service_CloudHealthcare_TestIamPermissionsRequest $postBody, $optParams = array())
  {
    $params = array('resource' => $resource, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', array($params), "Google_Service_CloudHealthcare_TestIamPermissionsResponse");
  }
}
