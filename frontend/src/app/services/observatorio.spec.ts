import { TestBed } from '@angular/core/testing';

import { Observatorio } from './observatorio';

describe('Observatorio', () => {
  let service: Observatorio;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(Observatorio);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
