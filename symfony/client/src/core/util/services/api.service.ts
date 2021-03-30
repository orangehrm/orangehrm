import axios, {AxiosInstance, AxiosResponse} from 'axios';

export class APIService {
  private _http: AxiosInstance;
  private _baseUrl: string;
  private _apiSection: string;

  constructor(baseUrl: string, path: string) {
    this._baseUrl = baseUrl;
    this._apiSection = path;
    this._http = axios.create({
      baseURL: this._baseUrl,
      timeout: 3000,
    });
  }

  getAll(params?: object): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'Cache-Control':
        'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
    };
    return this._http.get(this._apiSection, {headers, params});
  }

  get(id: number, params?: object): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.get(`${this._apiSection}/${id}`, {headers, params});
  }

  create(data: any): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    };
    return this._http.post(this._apiSection, data, {headers});
  }

  update(id: number, data: any): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.put(`${this._apiSection}/${id}`, data, {headers});
  }

  delete(id: number): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.delete(`${this._apiSection}/${id}`, {headers});
  }

  public get http() {
    return this._http;
  }

  public get baseUrl() {
    return this._baseUrl;
  }

  public set apiSection(path: string) {
    this._apiSection = path;
  }
}
